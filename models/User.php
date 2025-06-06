<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Registrar un nuevo usuario
    public function register($data) {
        $this->db->query('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
        
        // Vincular valores
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        // Ejecutar consulta
        if ($this->db->execute()) {
            // Asignar rol por defecto (por ejemplo, 'user' con ID 2)
            $userId = $this->db->lastInsertId();
            return $this->assignDefaultRole($userId, 2); // 2 es el ID del rol 'user'
        } else {
            return false;
        }
    }

    // Asignar rol por defecto al usuario
    private function assignDefaultRole($userId, $roleId) {
        $this->db->query('INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':role_id', $roleId);
        
        return $this->db->execute();
    }

    // Iniciar sesión
    public function login($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if ($row) {
            $hashed_password = $row->password;
            if (password_verify($password, $hashed_password)) {
                return $row;
            }
        }

        return false;
    }

    // Encontrar usuario por email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Verificar si se encontró el email
        return $this->db->rowCount() > 0;
    }

    // Obtener usuario por ID
    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    // Registrar actividad del usuario
    public function logActivity($userId, $action, $details = '') {
        $this->db->query('INSERT INTO user_activities (user_id, action, details, ip_address, user_agent, created_at) 
                         VALUES (:user_id, :action, :details, :ip_address, :user_agent, NOW())');
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':action', $action);
        $this->db->bind(':details', $details);
        $this->db->bind(':ip_address', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $this->db->bind(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
        
        return $this->db->execute();
    }

    // Verificar si el usuario tiene un rol específico
    public function hasRole($userId, $roleName) {
        $this->db->query('SELECT r.name 
                         FROM roles r 
                         JOIN user_roles ur ON r.id = ur.role_id 
                         WHERE ur.user_id = :user_id AND r.name = :role_name');
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':role_name', $roleName);
        
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    // Obtener todos los roles de un usuario
    public function getUserRoles($userId) {
        $this->db->query('SELECT r.* 
                         FROM roles r 
                         JOIN user_roles ur ON r.id = ur.role_id 
                         WHERE ur.user_id = :user_id');
        
        $this->db->bind(':user_id', $userId);
        
        return $this->db->resultSet();
    }

    // Actualizar perfil de usuario
    public function updateProfile($data) {
        $this->db->query('UPDATE users SET name = :name, email = :email WHERE id = :id');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        
        return $this->db->execute();
    }

    // Actualizar contraseña
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }

    // Verificar si la contraseña actual es correcta
    public function verifyCurrentPassword($userId, $password) {
        $user = $this->getUserById($userId);
        if ($user) {
            return password_verify($password, $user->password);
        }
        return false;
    }
}