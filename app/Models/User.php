<?php
class User
{
    private $mysqli;
    private $table_name = "users";
    private $hashed_password;

    // Properties
    public $user_id;
    public $role_id;
    public $password;
    public $email;
    public $full_name;
    public $phone_1;
    public $phone_2;
    public $nationality;
    public $created;
    public $created_by;
    public $updated;
    public $updated_by;

    // Constructor
    public function __construct($db)
    {
        $this->mysqli = $db;
    }

    // Read users
    public function get()
    {
        $query = "SELECT user_id, role_id,  email, full_name, phone_1, phone_2, nationality, CREATED, CREATED_BY, UPDATED, UPDATED_BY FROM " . $this->table_name . " ORDER BY CREATED DESC";
        $stmt = $this->mysqli->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create user
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET role_id=:role_id, email=:email, password=:password, email=:email, full_name=:full_name, phone_1=:phone_1, phone_2=:phone_2, nationality=:nationality, CREATED_BY=:created_by";
        $stmt = mysqli_prepare($this->mysqli, $query);

        // Clean data
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->phone_1 = htmlspecialchars(strip_tags($this->phone_1));
        $this->phone_2 = htmlspecialchars(strip_tags($this->phone_2));
        $this->nationality = htmlspecialchars(strip_tags($this->nationality));
        $this->created_by = htmlspecialchars(strip_tags($this->created_by));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Bind data
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bind_param("ssssssss", $this->role_id, $hashed_password, $this->email, $this->full_name, $this->phone_1, $this->phone_2, $this->nationality, $this->created_by);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read single user
    public function readOne()
    {
        $query = "SELECT user_id, role_id,  email, full_name, phone_1, phone_2, nationality, CREATED, CREATED_BY, UPDATED, UPDATED_BY FROM " . $this->table_name . " WHERE user_id = ? LIMIT 0,1";
        $stmt = $this->mysqli->prepare($query);

        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->role_id = $row['role_id'];
        $this->email = $row['email'];
        $this->full_name = $row['full_name'];
        $this->phone_1 = $row['phone_1'];
        $this->phone_2 = $row['phone_2'];
        $this->nationality = $row['nationality'];
        $this->created = $row['CREATED'];
        $this->created_by = $row['CREATED_BY'];
        $this->updated = $row['UPDATED'];
        $this->updated_by = $row['UPDATED_BY'];
    }

    // Update user
    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET role_id=:role_id,  email=:email, full_name=:full_name, phone_1=:phone_1, phone_2=:phone_2, nationality=:nationality, UPDATED_BY=:updated_by WHERE user_id=:user_id";
        $stmt = $this->mysqli->prepare($query);

        // Clean data
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->phone_1 = htmlspecialchars(strip_tags($this->phone_1));
        $this->phone_2 = htmlspecialchars(strip_tags($this->phone_2));
        $this->nationality = htmlspecialchars(strip_tags($this->nationality));
        $this->updated_by = htmlspecialchars(strip_tags($this->updated_by));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        // Bind data
        $stmt->bindParam(":role_id", $this->role_id);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":phone_1", $this->phone_1);
        $stmt->bindParam(":phone_2", $this->phone_2);
        $stmt->bindParam(":nationality", $this->nationality);
        $stmt->bindParam(":updated_by", $this->updated_by);
        $stmt->bindParam(":user_id", $this->user_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete user
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bindParam(1, $this->user_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Function to determine the home URL based on the user's role
    public function determineHomeURL($role_id)
    {
        switch ($role_id) {
            case 1:
                // Return admin dashboard URL
                return BASE_URL . "app/Views/users/dashboard_role/dashboard_admin.php";
            case 2:
                // Return owner dashboard URL
                return BASE_URL . "app/Views/users/dashboard_role/dashboard_owner.php";
            case 3:
                // Return tenant dashboard URL
                return BASE_URL . "app/Views/users/dashboard_role/dashboard_tenant.php";
            default:
                // Handle other cases or errors
                return BASE_URL . "index.php";
        }
    }

    public function register($name, $surname, $phone_1, $phone_2, $nationality, $email, $password, $repeatpassword)
    {
        $response = ['errors' => [], 'success' => ''];
        // Validation checks
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = "Email is not valid";
    }

    if (strlen($password) < 8) {
        $response['errors'][] = "Password must be at least 8 characters long";
    }

    if ($password !== $repeatpassword) {
        $response['errors'][] = "Passwords do not match";
    }

    // Check if the email or phone_1 already exists
    $sql = "SELECT * FROM users WHERE email = ? OR phone_1 = ?";
    if ($stmt = $this->mysqli->prepare($sql)) {
        $stmt->bind_param("ss", $email, $phone_1);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['email'] === $email) {
                        $response['errors'][] = "Cannot create an account. Email already exists.";
                    }
                    if ($row['phone_1'] === $phone_1) {
                        $response['errors'][] = "Cannot create an account. Phone number already exists.";
                    }
                }
            }
        } else {
            $response['errors'][] = "Error executing query.";
        }
        $stmt->close();
    } else {
        $response['errors'][] = "Error preparing query.";
    }

    // Insert into users table if there are no errors
    if (empty($response['errors'])) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role_id = 1; // Default role for new users
        $sql = "INSERT INTO users (name, surname, phone_1, phone_2, nationality, email, password, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $this->mysqli->prepare($sql)) {
            $stmt->bind_param("sssssssi", $name, $surname, $phone_1, $phone_2, $nationality, $email, $hashedPassword, $role_id);
            if ($stmt->execute()) {
                $response['success'] = "Account created successfully. Please log in.";
            } else {
                $response['errors'][] = "Error saving user information.";
            }
            $stmt->close();
        } else {
            $response['errors'][] = "Error preparing the insert statement.";
        }
    }

    return $response;
    }

}



