<?php
include 'db.php'; // Ensure the database connection is included

$query = "SELECT id, name, experties,image FROM perfumers";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$output = "";
while ($row = mysqli_fetch_assoc($result)) {
    $output .= "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['experties']}</td>
<td>{$row['image']}</td>
                    <td>
                        <button class='btn btn-warning btn-sm editPerfumer' data-id='{$row['id']}'>Edit</button>
                        <button class='btn btn-danger btn-sm deletePerfumer' data-id='{$row['id']}'>Delete</button>
                    </td>
                </tr>";
}
echo $output;
?>
