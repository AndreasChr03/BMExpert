<?php
class Property
{
    private $mysqli;
    private $table_name = "property";
    private $photo_path_table = "property_photos";
    private $propertyId;
    private $buildingId;
    private $ownerId;
    private $tenantId;
    private $floor;
    private $number;
    private $status;
    private $pet;
    private $furnished;
    private $rooms;
    private $bathrooms;
    private $parking;
    private $area;
    private $details;
    private $propertyVideos;
    private $comment;


    public function __construct($db)
    {
        $this->mysqli = $db;
    }

    // Get user_id from email
    public function getUserId($email)
    {
        $sql = "SELECT user_id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($this->mysqli, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row["user_id"];
    }

    // Check if the owner has any properties
    public function hasProperties($user_id)
    {
        $query = "SELECT * FROM property WHERE owner_id = ? LIMIT 0,1";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function getPropertyId()
    {
        return $this->propertyId;
    }

    // Create property
    public function insertProperty()
    {
        $query = "INSERT INTO " . $this->table_name . " SET building_id=?, owner_id=?, tenant_id=?, floor=?, number=?, status=?, pet=?, furnished=?, rooms=?, bathrooms=?, parking=?, area=?, details=?, comment=?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("iiiiisssiisiss", $this->buildingId, $this->ownerId, $this->tenantId, $this->floor, $this->number, $this->status, $this->pet, $this->furnished, $this->rooms, $this->bathrooms, $this->parking, $this->area, $this->details, $this->comment);

        return $stmt->execute();
    }
    // Add property photo
    public function addPhoto($property_id, $_files)
    {
        $upload_dir = "../../../../../public/img/uploads/";
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');

        // Loop through each photo file
        foreach ($_files['photo']['tmp_name'] as $key => $value) {
            // Get file details
            $file_name = $_files["photo"]['name'][$key];
            $file_tmp_name = $_files["photo"]['tmp_name'][$key];
            $file_error = $_files["photo"]['error'][$key];
            $file_size = $_files["photo"]['size'][$key];
            $file_type = pathinfo($file_name, PATHINFO_EXTENSION);

            if (in_array($file_type, $allowed_types)) {
                if ($file_error !== 0) {
                    echo "Error uploading file!";
                    continue;
                }

                if ($file_size > 4194304) { // 4MB limit
                    echo "File too large! Max size: 4MB";
                    continue;
                }

                $new_filename = $property_id . "_" . uniqid() . "." . pathinfo($file_name, PATHINFO_EXTENSION);
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($file_tmp_name, $upload_path)) {
                    $sql = "INSERT INTO property_photos (property_id, photo_path) VALUES (?, ?)";
                    $stmt = $this->mysqli->prepare($sql);
                    $stmt->bind_param("is", $property_id, $new_filename);

                    if ($stmt->execute()) {
                        echo "Photo uploaded and stored successfully!";
                    } else {
                        echo "Error storing photo in database!";
                    }

                    $stmt->close();
                } else {
                    echo "Error moving uploaded file!";
                }
            } else {
                echo "File type not allowed!";
            }
        }

    }

    public function showProperty($property_id)
    {
        $this->propertyId = $property_id;
        $sql = "SELECT * FROM property WHERE property_id = ?";
        $stmt = mysqli_prepare($this->mysqli, $sql);
        mysqli_stmt_bind_param($stmt, "i", $this->propertyId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result;
    }

    public function showAllPropertiesofUser($user_id)
    {
        // $this->ownerId=$user_id;
        $sql = "SELECT * FROM property WHERE owner_id = ?";
        $stmt = mysqli_prepare($this->mysqli, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result;
    }

    public function getPropertyPhotos($property_id)
    {
        $this->propertyId = $property_id;
        $sql = "SELECT * FROM property_photos WHERE property_id = ?";
        $stmt = mysqli_prepare($this->mysqli, $sql);
        mysqli_stmt_bind_param($stmt, "i", $this->propertyId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result;
    }

    //Setters and Getters for all variables
    public function setBuildingId($buildingId)
    {
        $this->buildingId = $buildingId;
    }
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }
    public function setTenantId($tenantId)
    {
        $this->tenantId = $tenantId;
    }
    public function setFloor($floor)
    {
        $this->floor = $floor;
    }
    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function setPet($pet)
    {
        $this->pet = $pet;
    }
    public function setFurnished($furnished)
    {
        $this->furnished = $furnished;
    }

    public function setRooms($rooms)
    {
        $this->rooms = $rooms;
    }

    public function setBathrooms($bathrooms)
    {
        $this->bathrooms = $bathrooms;
    }

    public function setParking($parking)
    {
        $this->parking = $parking;
    }

    public function setArea($area)
    {
        $this->area = $area;
    }

    public function setDetails($details)
    {
        $this->details = $details;
    }

    // public function setPropertyVideos($propertyVideos)
    // {
    //     $this->propertyVideos = $propertyVideos;
    // }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }


}
