<?php
$result = $con->query("SELECT * FROM contacts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Contacts - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 1px 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container table-container">
    <h2>📋 All Contact Leads</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No contact entries found.</div>
    <?php endif; ?>
</div>

</body>
</html>
