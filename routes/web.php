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
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PublicCustomFormController;
use App\Http\Controllers\ToolController;
use App\Models\CustomForm;
use App\Models\EmployeeRequest;
use App\Models\CompanyEvent;
use App\Models\EmployeeAppointment;
use App\Models\News;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
            ['label' => 'Solicitudes', 'value' => EmployeeRequest::count(), 'href' => '/employee-requests', 'permission' => 'employee.requests.manage'],
            ['label' => 'Citas', 'value' => EmployeeAppointment::count(), 'href' => '/appointments', 'permission' => 'appointments.manage'],
            ['label' => 'Eventos', 'value' => CompanyEvent::count(), 'href' => '/events', 'permission' => 'events.manage'],
            ['label' => 'Noticias', 'value' => News::count(), 'href' => '/admin/news', 'permission' => 'news.manage'],
        ],
        'recentUsers' => User::query()->latest()->limit(5)->get(['id', 'name', 'email', 'created_at']),
        'recentRequests' => EmployeeRequest::query()->with('user:id,name,email')->latest()->limit(5)->get(),
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
