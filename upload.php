<?php

$uploaddir = 'frames/';

if (is_uploaded_file($_FILES['file']['tmp_name']))
{
    $uploadfile = $uploaddir . basename($_FILES['file']['name']);
    
    echo "File " . $_FILES['file']['name'] . " uploaded successfully.";
    
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile))
    {
        echo "File upload succesful!";
    }
    else 
    {
        print_r($_FILES);
    }
}
else
{
    echo "Upload Failed!";
    print_r($_FILES);
}

?>