<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khách hàng</title>

    <!-- DataTable -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.13.1/datatables.min.css"/>

</head>

<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
    }

    .table1 {
        position: relative;
        top: 60px;
        left: 270px;
        width: 1400px;
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

<body>
@extends('admin-layout/menuadmin')
@section('content2')
    <div>
        <h2 style="position: relative; right: -270px; top: 15px">Quản lý khách hàng</h2>
        <br>
        <button type="button" style="position: relative; right: -270px; top: 40px" class="btn btn-warning" onclick="window.location.href='{{URL::asset('/admin/quanlykhachhang/lock')}}';">Xem các tài khoản khách hàng bị khóa</button>
        @if (session('success'))
            <script>
                window.onload = function() {
                    // Display the message box
                    Swal.fire({
                        text: "{{ session('success') }}",
                        textColor: 'black',
                        icon: 'success',
                        confirmButtonText: 'OK',
                    })
                }
            </script>
        @endif

        @if (session('deleteDone'))
            <script>
                window.onload = function() {
                    // Display the message box
                    Swal.fire({
                        text: "{{ session('deleteDone') }}",
                        textColor: 'black',
                        icon: 'success',
                        confirmButtonText: 'OK',
                    })
                }
            </script>
        @endif

        @if (session('editDone'))
            <script>
                window.onload = function() {
                    // Display the message box
                    Swal.fire({
                        text: "{{ session('editDone') }}",
                        textColor: 'black',
                        icon: 'success',
                        confirmButtonText: 'OK',
                    })
                }
            </script>
        @endif

        <div class="table1">
            <table id="hosobacsi" class="table table-bordered border-dark" style="width: 100%">
                <!-- tiêu đề bảng -->
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Tên đăng nhập</th>
                    <th>Mật khẩu</th>
                    <th>Ngày tạo tài khoản</th>
                    <th>Thao tác</th>
                    <th>Thao tác</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <!-- thân bảng -->
                <tbody>
                @forelse($accounts as $accounts)
                    <tr>
                        <td>{{ $accounts->id }}</td>
                        <td>{{ $accounts->name }}</td>
                        <td>{{ $accounts->username }}</td>
                        <td>.....</td>
                        <td>{{ date('d/m/Y, H:i', strtotime($accounts->created_at)) }}</td>
                        <td>
                            <form action="{{ url('/admin/quanlykhachhang/edit/' . $accounts->id)}}" method="GET">
                                <button type="submit" class="btn btn-outline-warning">Sửa tài khoản</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ url('/admin/quanlykhachhang/delete/'. $accounts->id)}}" method="GET">
                                <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản (Thao tác này không thể quay lại!)?')" class="btn btn-outline-danger">Xóa tài khoản</button>
                            </form>
                        </td>

                        <td>
                            <form action="{{ url('/admin/quanlykhachhang/lock/' . $accounts->id) }}" method="POST">
                                <button type="submit" class="btn btn-outline-danger" onclick="confirm('Bạn muốn khóa tài khoản này?')">Khóa tài khoản</button>
                            </form>
                        </td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
</body>
<!-- DataTable -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.13.1/datatables.min.js"></script>

<script>
    $(document).ready(function () {
        $.fn.dataTableExt.sErrMode = 'throw';
        $('#hosobacsi').DataTable({
            language: {
                search: "Tìm kiếm",
                lengthMenu: "Hiển thị 1 trang _MENU_ cột",
                info: "Bản ghi từ _START_ đến _END_ Tổng cộng _TOTAL_",
                infoEmpty: "0 bản ghi trong 0 tổng cộng 0",
                zeroRecords: "Không có lịch hoặc dữ liệu bạn tìm kiếm",
                emptyTable: "Chưa có bác sĩ nào",
                paginate: {
                    first: "Trang đầu",
                    previous: "Trang trước",
                    next: "Trang sau",
                    last: "Trang cuối"
                },
            },
        });

    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</html>
