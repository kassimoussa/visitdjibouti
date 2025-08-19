<?php

namespace App\Livewire\Admin;

use App\Models\AdminUser;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Hash;

class UserManager extends Component  
{
    use WithPagination;
    
    // Bootstrap pagination
    protected $paginationTheme = 'bootstrap';
    
    // Propriétés pour la recherche et le tri
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedRole = '';
    public $statusFilter = '';
    
    // Propriétés pour le formulaire
    public $user_id;
    public $name;
    public $email;
    public $phone_number;
    public $role_id;
    public $is_active = true;
    public $password;
    public $password_confirmation;
    
    // États des modales
    public $isOpenCreateModal = false;
    public $isOpenEditModal = false;
    public $isOpenDeleteModal = false;
    
    protected $listeners = ['refreshUsers' => '$refresh'];
    
    protected function rules()
    {
        $rules = [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ];
        
        // Validation additionnelle pour la création d'utilisateur
        if ($this->isOpenCreateModal) {
            $rules['email'] .= '|unique:admin_users,email';
            $rules['password'] = 'required|min:8|confirmed';
            $rules['password_confirmation'] = 'required';
        }
        
        // Validation pour la mise à jour d'utilisateur
        if ($this->isOpenEditModal) {
            $rules['email'] .= '|unique:admin_users,email,' . $this->user_id;
            $rules['password'] = 'nullable|min:8|confirmed';
        }
        
        return $rules;
    }
    
    #[Title('Gestion des utilisateurs administrateurs')]
    public function render()
    {
        $users = $this->getUsers();
        $roles = Role::all();
        
        return view('livewire.admin.users.user-manager', [
            'users' => $users,
            'roles' => $roles
        ]);
    }
    
    public function getUsers()
    {
        $query = AdminUser::with('role');
        
        // Recherche sur plusieurs champs
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('role', function($roleQuery) {
                      $roleQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        // Filtrage par rôle
        if (!empty($this->selectedRole)) {
            $query->where('role_id', $this->selectedRole);
        }
        
        // Filtrage par statut actif
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter == '1');
        }
        
        // Tri des résultats
        if ($this->sortField === 'role') {
            $query->join('roles', 'admin_users.role_id', '=', 'roles.id')
                  ->orderBy('roles.name', $this->sortDirection)
                  ->select('admin_users.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }
        
        return $query->paginate(10);
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }
    
    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset([
            'name', 'email', 'phone_number', 'role_id', 'is_active', 
            'password', 'password_confirmation'
        ]);
        $this->is_active = true;
        
        // Définir le premier rôle disponible comme valeur par défaut
        $firstRole = Role::first();
        if ($firstRole) {
            $this->role_id = $firstRole->id;
        }
        
        $this->isOpenCreateModal = true;
        $this->dispatch('show-create-modal');
    }
    
    public function closeCreateModal()
    {
        $this->isOpenCreateModal = false;
        $this->dispatch('hide-create-modal');
    }
    
    public function create()
    {
        $this->validate();
        
        AdminUser::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'role_id' => $this->role_id,
            'is_active' => $this->is_active,
            'password' => Hash::make($this->password),
        ]);
    
        $this->isOpenCreateModal = false;
        $this->dispatch('hide-create-modal');
        $this->dispatch('show-toast', message: 'Utilisateur créé avec succès !', type: 'success');
    }
    
    public function openEditModal($userId)
    {
        $this->resetValidation();
        $this->user_id = $userId;
        $user = AdminUser::findOrFail($userId);
        
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->role_id = $user->role_id;
        $this->is_active = $user->is_active;
        $this->password = '';
        $this->password_confirmation = '';
    
        $this->isOpenEditModal = true;
        $this->dispatch('show-edit-modal');
    }
    
    public function closeEditModal()
    {
        $this->isOpenEditModal = false;
        $this->dispatch('hide-edit-modal');
    }
    
    public function update()
    {
        $this->validate();
        
        $user = AdminUser::findOrFail($this->user_id);
        
        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'role_id' => $this->role_id,
            'is_active' => $this->is_active,
        ];
        
        // Mise à jour du mot de passe uniquement s'il est fourni
        if (!empty($this->password)) {
            $userData['password'] = Hash::make($this->password);
        }
        
        $user->update($userData);
    
        $this->isOpenEditModal = false;
        $this->dispatch('hide-edit-modal');
        $this->dispatch('show-toast', message: 'Utilisateur mis à jour avec succès !', type: 'success');
    }
    
    public function openDeleteModal($userId)
    {
        $this->user_id = $userId;
        $this->isOpenDeleteModal = true;
        $this->dispatch('show-delete-modal');
    }
    
    public function closeDeleteModal()
    {
        $this->isOpenDeleteModal = false;
        $this->dispatch('hide-delete-modal');
    }
    
    public function delete()
    {
        $user = AdminUser::findOrFail($this->user_id);
        
        // Vérifier si ce n'est pas le dernier admin actif
        if ($user->hasRole('admin') && AdminUser::where('is_active', true)->whereHas('role', function($q) {
            $q->where('slug', 'admin');
        })->count() <= 1) {
            $this->dispatch('show-toast', message: 'Impossible de supprimer le dernier administrateur actif !', type: 'error');
            return;
        }
        
        $user->delete();
        
        $this->isOpenDeleteModal = false;
        $this->dispatch('hide-delete-modal');
        $this->dispatch('show-toast', message: 'Utilisateur supprimé avec succès !', type: 'success');
    }
    
    public function toggleUserStatus($userId)
    {
        $user = AdminUser::findOrFail($userId);
        
        // Vérifier si ce n'est pas le dernier admin actif
        if ($user->is_active && $user->hasRole('admin') && AdminUser::where('is_active', true)->whereHas('role', function($q) {
            $q->where('slug', 'admin');
        })->count() <= 1) {
            $this->dispatch('show-toast', message: 'Impossible de désactiver le dernier administrateur actif !', type: 'error');
            return;
        }
        
        $user->is_active = !$user->is_active;
        $user->save();
        
        $status = $user->is_active ? 'activé' : 'désactivé';
        $this->dispatch('show-toast', message: "Utilisateur {$status} avec succès !", type: 'success');
    }
    
    public function resetFilters()
    {
        $this->reset(['search', 'selectedRole', 'statusFilter']);
    }
}