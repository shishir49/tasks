<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{csrf_token()}}">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->

        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  

        <style>
            table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            }

            td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            }

            tr:nth-child(even) {
            background-color: #dddddd;
            }
        </style>
        
        <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
        <script src="{{asset('js/jquery.js')}}"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h3>Customer Record Management</h3>
                </div>
                <div class="col-12 my-2">
                    <div class="card">
                        <div class="card-header">
                            <h6>Task 2 : Move pdf files in proper folders</h6>
                        </div>
                        <div class="card-body">
                            <span class="card-text text-danger">Please Check "resources/pdfFiles" Folder where The pdf files are stored initially</span><br>
                            <span class="card-text text-danger">The Folders with associate company names will be generated in "public/upload" folder once the button (bellow) is clicked</span><br>
                            <span class="card-text text-danger">When the Button (bellow) is clicked, it will automatically push "task.csv" file to the controller which is in the "public" folder</span><br>
                            <form action="{{url('store-pdf')}}" method="POST" id="store-pdf">
                                @csrf
                                <button class="btn btn-success" type="submit">Move Files</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 my-2">
                    <div class="card">
                        <div class="card-header">
                            <h6>Task 1 : Upload CSV File</h6>
                        </div>
                        <div class="card-body">
                        <span class="text-danger">The csv can be imported batch/chunk wise with queue from a html view</stpan><br>
                        <span class="text-danger">Imported data is shown in a normal table</span><br>
                        <span class="text-danger">Branch wise ajax filtering option for html table is Added</span><br>
                        <span class="text-danger">Gender wise ajax filtering option for html table is Added</span><br>
                        <span class="text-danger">Email will be send to admin@akaarit.com 30 second after import completes</stronpan><br><br>
                            <form action="{{url('store-csv-data')}}" method="POST" enctype="multipart/form-data" id="upload-csv">
                               
                                <input type="file" name="uploadCsv" id="uploadCsv" class="form-controller">
                                <button class="btn btn-success" type="submit">Upload Records</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 my-2">
                    <div class="card">
                        <div class="card-header">
                        <h6>Customer Records</h6>
                        </div>
                        <div class="card-body">
                            <form id="search">
                                <div class="row">
                                    <div class="col-4">
                                        <select name="" class="form-control" id="branch_id">
                                            <option value="">Search By Branch</option>
                                            <option value="1">Branch Number 1</option>
                                            <option value="2">Branch Number 2</option>
                                        </select>
                                    </div>

                                    <div class="col-4">
                                        <select name="" class="form-control" id="gender">
                                            <option value="">Search by Gender</option>
                                            <option value="M">Male</option>
                                            <option value="F">Female</option>
                                        </select>
                                    </div>

                                    <div class="col-4">
                                        <button type="submit" class="btn btn-success">Search</button>
                                    </div>
                                </div>
                            </form>

                            <div class="col-12 my-2">
                                <table>
                                    <thead>
                                        <tr>
                                            <td>ID</td>
                                            <td>Branch ID</td>
                                            <td>First Name</td>
                                            <td>Last Name</td>
                                            <td>Email</td>
                                            <td>Phone</td>
                                            <td>Gender</td>
                                        </tr>
                                    </thead>
                                    <tbody id="fetch-data">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function(){

                // Store CSV Data

                $('#upload-csv').on('submit', function(e){
                    e.preventDefault();
                    var formData = new FormData(this)

                    $.ajax({
                        type : 'POST',
                        url : '/store-csv-data',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data : formData,
                        contentType: false,
                        processData: false,
                        success : function(data){
                            alert('File Successfully Uploaded !')
                            getCustomer();
                        },

                        error : function() {
                            alert("Please Check your php Config");
                        }
                    })
                })

                // Fetch Customer Data in table 
                $('#search').on('submit', function(e){
                    e.preventDefault();

                    let branch_id = $('#branch_id').val();
                    let gender    = $('#gender').val();

                    getCustomer(branch_id, gender);
                });

                function getCustomer(branch_id = '' , gender = '') {
                    $.ajax({
                        type : 'POST',
                        url : '/get-customer',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data : {branch_id : branch_id, gender : gender},
                        dataType : 'json',
                        success : function(data){
                        console.log(data);
                        var getHtml = '';

                        if(data.length  > 0 ) {
                            $.each(data, function(i, data){
                                getHtml +=`
                                    <tr>
                                        <td>${Number(i) + 1}</td>
                                        <td>${data.branch_id}</td>
                                        <td>${data.first_name}</td>
                                        <td>${data.last_name}</td>
                                        <td>${data.email}</td>
                                        <td>${data.phone}</td>
                                        <td>${data.gender}</td>
                                    </tr>
                                `;
                            })
                        }else{
                            getHtml +=`
                                <td colspan='7' class='text-center'>No Customer Found</td>
                            `;
                        }

                        $('#fetch-data').html(getHtml);
                        }
                    })
                }

                getCustomer();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
            })
        </script>
    </body>
</html>
