<?php

include "Database/Connection.php";

class Controller
{

    private function getConnection(){
        $connection = new Connection();
        return $connection->getConnection();
    }

    public function create($name,$lastname,$surname,$phone){
        $connection = $this->getConnection();
        try {
            $existe = false;

            /**
             * Recorremos la base de datos para mirar si el contacto existe
             */
            foreach ($connection->query("SELECT \"Phone\" FROM oasiao_agenda_db.public.contacts") as $contact) {
                if ($contact['Phone'] === $phone) {
                    $existe = true; //si coincide alguno, entonces retornamos true
                    break;
                }
            }

            $query = $connection->prepare("INSERT INTO oasiao_agenda_db.public.contacts 
                     (\"Name\", \"Surname\", \"Lastname\", \"Phone\") 
                    VALUES (:name,:surname,:lastname,:phone);");

            if (!$existe) {
                $query->execute([
                    ':name' => $name,
                    ':surname' => $surname,
                    ':lastname'=> $lastname,
                    ':phone' => $phone
                    ]);
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            echo "Error in adding a new contact!";
            die();
        }
    }

    public function read(){
        /*EN ESTA FUNCIÓN, NO HACE FALTA PREPARAR LA QUERY PORQUE NO LE PASAMOS PARÁMETROS.*/
        $connection = $this->getConnection();
        $output = "<table border 1px solid style='margin: 10vh;'><tr>";
        $output .= "<th>Name</th><th>Lastname</th><th>Surname</th><th>Phone</th></tr>";
        foreach ($connection->query("SELECT * FROM oasiao_agenda_db.public.contacts") as $contact) {
            $output .= "<tr>";
            $output .= '<th>' . $contact['Name'] . '</th>';
            $output .= '<th>' . $contact['Lastname'] . '</th>';
            $output .= '<th>' . $contact['Surname'] . '</th>';
            $output .= '<th>' . $contact['Phone'] . '</th>';
            $output .= "</tr>";
        }
        $output .= "</table>";
        return $output;
    }

    public function update($name,$lastname,$surname,$phone){
        $connection = $this->getConnection();
        try {
            $existeTelefono = false;
            $existeNombre = false;

            foreach ($connection->query("SELECT * FROM oasiao_agenda_db.public.contacts") as $contact) {
                if ($contact['Phone'] === $phone) {
                    $existeTelefono = true;
                    break;
                }else if($contact['Name'] === $name && $contact['Lastname']===$lastname && $contact['Surname'] === $surname){
                    $existeNombre = true;
                }
            }

            if ($existeTelefono){
                $query = $connection->prepare("UPDATE oasiao_agenda_db.public.contacts SET 
                                            \"Name\" = :name, \"Lastname\" = :lastname, \"Surname\" = :surname, 
                                            \"Phone\" = :phone WHERE \"Phone\" = :phone");

                $query->execute([
                    ':name' => $name,
                    ':surname' => $surname,
                    ':lastname'=> $lastname,
                    ':phone' => $phone
                ]);

                return true; //si no existe, enviamos un mensaje de "Bien hecho!"
            }
            else if($existeNombre){
                $query = $connection->prepare("UPDATE oasiao_agenda_db.public.contacts SET \"Name\" = :name, \"Lastname\" = :lastname, 
                                            \"Surname\" = :surname, \"Phone\" = :phone WHERE \"Name\" = :name 
                                            and \"Surname\" = :surname and \"Lastname\" = :lastname");

                $query->execute([
                    ':name' => $name,
                    ':surname' => $surname,
                    ':lastname'=> $lastname,
                    ':phone' => $phone
                ]);
                return true;
            }
            else{
                return false; //si existe, nos mostrará un mensaje de error
            }


        } catch (PDOException $e) {
            echo "Error in updating contact!";
            die();
        }
    }

    public function delete($phone){
        $connection = $this->getConnection();
        try {
            $existe = false;

            foreach ($connection->query("SELECT * FROM oasiao_agenda_db.public.contacts") as $contact) {
                if ($contact['Phone'] === $phone) {
                    $existe = true;
                    break;
                }
            }

            $query = $connection->prepare("DELETE FROM oasiao_agenda_db.public.contacts WHERE \"Phone\" = :phone");


            if ($existe === true) {
                $query->execute([
                    ':phone' => $phone
            ]);
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            echo "Error in updating contact!";
            die();
        }

    }


}