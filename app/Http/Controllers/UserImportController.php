<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserImportController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the import form
     */
    public function showImportForm()
    {
        $this->authorize('create_user');
        
        return view('users.import');
    }

    /**
     * Process the Excel/CSV import
     */
    public function import(Request $request)
    {
        $this->authorize('create_user');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            if ($extension === 'csv') {
                $users = $this->parseCSV($file);
            } else {
                $users = $this->parseExcel($file);
            }

            $imported = 0;
            $errors = [];

            foreach ($users as $index => $userData) {
                $validator = Validator::make($userData, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'institutional_email' => 'nullable|email|unique:users,institutional_email',
                    'password' => 'required|string|min:8',
                    'role' => 'required|exists:roles,name',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Fila " . ($index + 2) . ": " . implode(', ', $validator->errors()->all());
                    continue;
                }

                try {
                    $user = User::create([
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'institutional_email' => $userData['institutional_email'] ?? null,
                        'password' => Hash::make($userData['password']),
                    ]);

                    $user->assignRole($userData['role']);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Fila " . ($index + 2) . ": Error al crear usuario - " . $e->getMessage();
                }
            }

            $message = "$imported usuarios importados exitosamente.";
            if (count($errors) > 0) {
                $message .= " " . count($errors) . " errores encontrados.";
            }

            return redirect()->route('users.index')
                           ->with('success', $message)
                           ->with('import_errors', $errors);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Parse CSV file
     */
    private function parseCSV($file)
    {
        $users = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header row
        $header = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 4) {
                $users[] = [
                    'name' => $row[0] ?? '',
                    'email' => $row[1] ?? '',
                    'institutional_email' => $row[2] ?? '',
                    'password' => $row[3] ?? '',
                    'role' => $row[4] ?? 'Estudiante',
                ];
            }
        }
        
        fclose($handle);
        return $users;
    }

    /**
     * Parse Excel file (requires PhpSpreadsheet)
     */
    private function parseExcel($file)
    {
        // Check if PhpSpreadsheet is available
        if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            throw new \Exception('PhpSpreadsheet no está instalado. Por favor, use archivos CSV o instale PhpSpreadsheet.');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        $users = [];
        
        // Skip header row
        array_shift($rows);
        
        foreach ($rows as $row) {
            if (count($row) >= 4 && !empty($row[0])) {
                $users[] = [
                    'name' => $row[0] ?? '',
                    'email' => $row[1] ?? '',
                    'institutional_email' => $row[2] ?? '',
                    'password' => $row[3] ?? '',
                    'role' => $row[4] ?? 'Estudiante',
                ];
            }
        }
        
        return $users;
    }

    /**
     * Download template file
     */
    public function downloadTemplate()
    {
        $this->authorize('create_user');

        $filename = 'plantilla_usuarios.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = ['Nombre', 'Email', 'Email Institucional', 'Contraseña', 'Rol'];
        $example = [
            'Juan Pérez',
            'juan.perez@example.com',
            'juan.perez@iestp.edu.pe',
            'password123',
            'Estudiante'
        ];

        $callback = function() use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
