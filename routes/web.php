<?php

use App\Http\Controllers\AccessUserController;
use App\Http\Controllers\AccessPermissionController;
use App\Http\Controllers\AccessRoleController;
use App\Http\Controllers\AdminNewsController;
use App\Http\Controllers\ContactAssistantController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\CustomFormController;
use App\Http\Controllers\CompanyEventController;
use App\Http\Controllers\EmployeeAppointmentController;
use App\Http\Controllers\EmployeeRequestController;
use App\Http\Controllers\EventRequestController;
use App\Http\Controllers\EventRequestStageController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PublicCustomFormController;
use App\Http\Controllers\ToolController;
use App\Models\CustomForm;
use App\Models\EmployeeRequest;
use App\Models\EventRequest;
use App\Models\CompanyEvent;
use App\Models\EmployeeAppointment;
use App\Models\News;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


Route::get('/test-error', function () {
    // Lanza un error para probar el manejo de errores
    abort(403);
})->name('test.error');

Route::get('/diagnostic-db', function () {
    try {
        DB::connection()->getPdo();

        $database = DB::connection()->getDatabaseName();
        $tablaSessionsExiste = Schema::hasTable('sessions');
        $registrosEnSessions = $tablaSessionsExiste ? DB::table('sessions')->count() : null;

        $env = [
            'APP_NAME' => config('app.name'),
            'APP_ENV' => config('app.env'),
            'APP_DEBUG' => config('app.debug'),
            'APP_URL' => config('app.url'),
        ];

        // Ejecuta migrate:status y captura la salida
        Artisan::call('migrate:status');
        $output = Artisan::output();

        // Verifica si hay migraciones pendientes buscando la palabra 'No'
        $migracionesPendientes = str_contains($output, 'No');

        return Inertia::render('Dashboard/DiagnosticoDB', [
            'conexion' => 'exitosa',
            'baseDeDatos' => $database,
            'tablaSessionsExiste' => $tablaSessionsExiste,
            'registrosEnSessions' => $registrosEnSessions,
            'variablesEnv' => $env,
            'migracionesPendientes' => $migracionesPendientes,
            'phpVersion' => phpversion(),
            'laravelVersion' => app()->version(),
        ]);

    } catch (\Exception $e) {
        return Inertia::render('Dashboard/DiagnosticDB', [
            'conexion' => 'fallida',
            'error' => $e->getMessage(),
        ]);
    }
})->name('diagnostic.db');

Route::middleware(['auth:sanctum', 'can:assign permissions'])->get('/clear-permission-cache', function () {
    Artisan::call('permission:cache-reset');
    Artisan::call('optimize:clear');

    return response()->json([
        'status' => 'success',
        'message' => 'Cache de permisos y optimización limpiados correctamente.',
    ]);
});

Route::get('/check-role', function (Request $request) {
    $user = auth()->user();

    $role = $request->query('role');
    $permission = $request->query('permission');

    $response = [
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name'),
    ];

    // Si se pasa el parámetro 'role', verifica si el usuario lo tiene
    if ($role) {
        $response['checking_role'] = $role;
        $response['has_role'] = $user->hasRole($role);
    }

    // Si se pasa el parámetro 'permission', verifica si el usuario lo tiene
    if ($permission) {
        $response['checking_permission'] = $permission;
        $response['has_permission'] = $user->can($permission);
    }

    return $response;
});



Route::get('/clear-cache', function () {
    try {
        $exitCode = Artisan::call('cache:clear');
        $exitCode = Artisan::call('route:clear');
        $exitCode = Artisan::call('view:clear');
        $exitCode = Artisan::call('config:cache');

        return 'DONE'; // Return anything
    } catch (Throwable $th) {
        // throw $th;
        return $th; // Return anything
    }
});

Route::get('/debug-paths', function () {
    return [
        'base_path' => base_path(),
        'public_path' => public_path(),
        'storage_path' => storage_path(),
    ];
});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');

    return 'Enlace de storage creado correctamente';
});

Route::get('/crear-storage', function () {

    $target = base_path('storage/app/public');
    $link = base_path('../public_html/storage');

    $info = [];

    $info['target_path'] = $target;
    $info['link_path'] = $link;
    $info['target_exists'] = file_exists($target) ? 'SI' : 'NO';
    $info['link_exists'] = file_exists($link) ? 'SI' : 'NO';
    $info['is_symlink'] = is_link($link) ? 'SI' : 'NO';

    if (is_link($link)) {
        $info['symlink_points_to'] = readlink($link);
    }

    if (file_exists($link)) {
        $info['mensaje'] = 'El enlace o carpeta ya existe';

        return response()->json($info);
    }

    try {

        if (symlink($target, $link)) {

            $info['mensaje'] = 'Enlace creado correctamente';
            $info['symlink_points_to'] = readlink($link);

        } else {

            $info['mensaje'] = 'No se pudo crear el enlace (posible restricción del hosting)';

        }

    } catch (\Throwable $e) {

        $info['error'] = $e->getMessage();
    }

    return response()->json($info);
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'notices' => News::query()->published()->latest('published_at')->limit(3)->get(),
    ]);
})->name('home');

Route::get('/about-us', function () {
    return Inertia::render('AboutUs');
})->name('about-us');

Route::get('/our-services', function () {
    return Inertia::render('OurServices');
})->name('our-services');

Route::get('/our-gallery', function () {
    return Inertia::render('OurGallery');
})->name('our-gallery');

Route::get('/contact-us', function () {
    return Inertia::render('ContactUs');
})->name('contact-us');

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news:slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/forms/{customForm:slug}', [PublicCustomFormController::class, 'show'])->name('forms.public.show');
Route::post('/forms/{customForm:slug}', [PublicCustomFormController::class, 'submit'])->middleware('throttle:10,1')->name('forms.public.submit');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard', [
        'stats' => [
            ['label' => 'Usuarios', 'value' => User::count(), 'href' => '/access/users', 'permission' => 'users.manage'],
            ['label' => 'Roles', 'value' => Role::count(), 'href' => '/access/roles', 'permission' => 'roles.manage'],
            ['label' => 'Permisos', 'value' => Permission::count(), 'href' => '/access/permissions', 'permission' => 'permissions.manage'],
            ['label' => 'Encuestas', 'value' => CustomForm::count(), 'href' => '/surveys', 'permission' => 'forms.manage'],
            ['label' => 'Solicitudes RRHH', 'value' => EmployeeRequest::count(), 'href' => '/employee-requests', 'permission' => 'employee.requests.manage'],
            ['label' => 'Solicitudes eventos', 'value' => EventRequest::count(), 'href' => '/event-requests', 'permission' => 'event.requests.manage'],
            ['label' => 'Citas', 'value' => EmployeeAppointment::count(), 'href' => '/appointments', 'permission' => 'appointments.manage'],
            ['label' => 'Eventos', 'value' => CompanyEvent::count(), 'href' => '/events', 'permission' => 'events.manage'],
            ['label' => 'Noticias', 'value' => News::count(), 'href' => '/admin/news', 'permission' => 'news.manage'],
        ],
        'recentUsers' => User::query()->latest()->limit(5)->get(['id', 'name', 'email', 'created_at']),
        'recentRequests' => EmployeeRequest::query()->with('user:id,name,email')->latest()->limit(5)->get(),
        'recentEventRequests' => EventRequest::query()->with('client:id,name,email')->latest()->limit(5)->get(['id', 'reference', 'title', 'stage_key', 'client_user_id', 'created_at']),
        'recentForms' => CustomForm::query()->latest()->limit(5)->get(['id', 'title', 'audience', 'is_active', 'created_at']),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('access/users', [AccessUserController::class, 'index'])->name('access.users.index');
    Route::post('access/users', [AccessUserController::class, 'store'])->name('access.users.store');
    Route::patch('access/users/{user}', [AccessUserController::class, 'update'])->name('access.users.update');
    Route::get('access/roles', [AccessRoleController::class, 'index'])->name('access.roles.index');
    Route::post('access/roles', [AccessRoleController::class, 'store'])->name('access.roles.store');
    Route::patch('access/roles/{role}', [AccessRoleController::class, 'update'])->name('access.roles.update');
    Route::delete('access/roles/{role}', [AccessRoleController::class, 'destroy'])->name('access.roles.destroy');
    Route::get('access/permissions', [AccessPermissionController::class, 'index'])->name('access.permissions.index');
    Route::post('access/permissions', [AccessPermissionController::class, 'store'])->name('access.permissions.store');
    Route::delete('access/permissions/{permission}', [AccessPermissionController::class, 'destroy'])->name('access.permissions.destroy');

    Route::get('employee-requests', [EmployeeRequestController::class, 'index'])->name('employee-requests.index');
    Route::post('employee-requests', [EmployeeRequestController::class, 'store'])->name('employee-requests.store');
    Route::patch('employee-requests/{employeeRequest}', [EmployeeRequestController::class, 'update'])->name('employee-requests.update');

    Route::get('event-requests', [EventRequestController::class, 'index'])->name('event-requests.index');
    Route::post('event-requests', [EventRequestController::class, 'store'])->name('event-requests.store');
    Route::get('event-requests/{eventRequest}', [EventRequestController::class, 'show'])->name('event-requests.show');
    Route::patch('event-requests/{eventRequest}', [EventRequestController::class, 'update'])->name('event-requests.update');
    Route::patch('event-requests/{eventRequest}/stage', [EventRequestController::class, 'updateStage'])->name('event-requests.stage.update');
    Route::post('event-requests/{eventRequest}/tasks', [EventRequestController::class, 'storeTask'])->name('event-requests.tasks.store');
    Route::patch('event-requests/{eventRequest}/tasks/{task}', [EventRequestController::class, 'updateTask'])->name('event-requests.tasks.update');
    Route::post('event-requests/{eventRequest}/comments', [EventRequestController::class, 'storeComment'])->name('event-requests.comments.store');
    Route::post('event-requests/{eventRequest}/attachments', [EventRequestController::class, 'storeAttachment'])->name('event-requests.attachments.store');
    Route::get('event-requests/{eventRequest}/attachments/{attachment}', [EventRequestController::class, 'downloadAttachment'])->name('event-requests.attachments.download');
    Route::delete('event-requests/{eventRequest}/attachments/{attachment}', [EventRequestController::class, 'destroyAttachment'])->name('event-requests.attachments.destroy');

    Route::get('admin/event-request-stages', [EventRequestStageController::class, 'index'])->name('event-request-stages.index');
    Route::post('admin/event-request-stages', [EventRequestStageController::class, 'store'])->name('event-request-stages.store');
    Route::patch('admin/event-request-stages/{eventRequestStage}', [EventRequestStageController::class, 'update'])->name('event-request-stages.update');
    Route::delete('admin/event-request-stages/{eventRequestStage}', [EventRequestStageController::class, 'destroy'])->name('event-request-stages.destroy');

    Route::get('appointments', [EmployeeAppointmentController::class, 'index'])->name('appointments.index');
    Route::post('appointments', [EmployeeAppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('appointments/{employeeAppointment}', [EmployeeAppointmentController::class, 'update'])->name('appointments.update');

    Route::get('events', [CompanyEventController::class, 'index'])->name('events.index');
    Route::post('events', [CompanyEventController::class, 'store'])->name('events.store');
    Route::post('events/{companyEvent}/employees', [CompanyEventController::class, 'assignEmployee'])->name('events.employees.store');
    Route::post('events/{companyEvent}/register', [CompanyEventController::class, 'register'])->name('events.register');
    Route::post('events/{companyEvent}/tools', [CompanyEventController::class, 'assignTool'])->name('events.tools.store');

    Route::get('tools', [ToolController::class, 'index'])->name('tools.index');
    Route::post('tools', [ToolController::class, 'store'])->name('tools.store');

    Route::get('admin/forms', [CustomFormController::class, 'index'])->name('admin.forms.index');
    Route::get('surveys', [CustomFormController::class, 'index'])->name('surveys.index');
    Route::post('admin/forms', [CustomFormController::class, 'store'])->name('admin.forms.store');
    Route::patch('admin/forms/{customForm:id}', [CustomFormController::class, 'update'])->name('admin.forms.update');
    Route::delete('admin/forms/{customForm:id}', [CustomFormController::class, 'destroy'])->name('admin.forms.destroy');

    Route::get('admin/news', [AdminNewsController::class, 'index'])->name('admin.news.index');
    Route::post('admin/news', [AdminNewsController::class, 'store'])->name('admin.news.store');
    Route::post('admin/news/import', [AdminNewsController::class, 'import'])->name('admin.news.import');
    Route::post('admin/news/import-social-profiles', [AdminNewsController::class, 'importSocialProfiles'])->name('admin.news.import-social-profiles');
    Route::patch('admin/news/{news:id}', [AdminNewsController::class, 'update'])->name('admin.news.update');
    Route::delete('admin/news/{news:id}', [AdminNewsController::class, 'destroy'])->name('admin.news.destroy');
});

Route::post('contact-assistant', [ContactAssistantController::class, 'store'])->name('contact-assistant.store');
Route::post('contact-us', [ContactMessageController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
