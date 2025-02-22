<head>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    
    <!-- jQuery (Required for DataTables) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- DataTables Buttons Extension -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- JSZip and PDFMake (Required for Excel and PDF Export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <style>
        /* Override DataTables Button Styles */
        .dt-buttons .btn {
            margin-right: 5px; /* Add spacing between buttons */
        }
    </style>
</head>

<body>
    <div class="container mt-3">
        <table id="myTable1" class="table table-bordered table-striped">
        <thead class="gradient-header">
                <tr>
                    <th>S.no</th>
                    <th>UserName</th>
                    <th>HallName</th>
                    <th>Time</th>
                    <th>Event</th>
                    <th>Requirements</th>
                   
                </tr>
            </thead>
            <tbody>
                <?php
             
                $query1 = "SELECT * FROM booking WHERE status='approved'";
                $result = mysqli_query($db, $query1);
                $serial_number = 1;

                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?php echo $serial_number; ?></td>
                        <td><?php echo htmlspecialchars($row['user']); ?></td>
                        <td><?php echo htmlspecialchars($row['hall']); ?></td>
                        <td><?php echo htmlspecialchars($row['time']); ?></td>
                        <td><?php echo htmlspecialchars($row['event']); ?></td>
                        <td><?php echo htmlspecialchars($row['req']); ?></td>
                    </tr>
                <?php
                    $serial_number++;
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- DataTable Initialization Script -->
    <script>
        $(document).ready(function() {
            $('#myTable1').DataTable({
                dom: '<"row"<"col-md-6"B><"col-md-6"f>>rtip',
                buttons: [
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-csv"></i> CSV',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel"></i> Excel',
                        className: 'btn btn-primary btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm'
                    }
                ]
            });
        });
    </script>
</body>
