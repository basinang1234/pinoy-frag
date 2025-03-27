<?php
require 'db.php';
require 'admin_dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reports - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Reports</h2>
        <table id="reportsTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Report Type</th>
                    <th>Reported By</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM reports");
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>
                        <td>' . $row['id'] . '</td>
                        <td>' . $row['report_type'] . '</td>
                        <td>' . $row['reported_by'] . '</td>
                        <td>' . $row['description'] . '</td>
                        <td>' . $row['report_date'] . '</td>
                        <td>' . $row['status'] . '</td>
                        <td>
                            <a href="view_report.php?id=' . $row['id'] . '" class="btn btn-info btn-sm">View</a>
                            <a href="delete_report.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\');">Delete</a>
                        </td>
                    </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Report Modal -->
    <div class="modal fade" id="addReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="add_report.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">Report Type</label>
                            <input type="text" name="report_type" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reported By</label>
                            <input type="text" name="reported_by" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="Pending">Pending</option>
                                <option value="Resolved">Resolved</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
