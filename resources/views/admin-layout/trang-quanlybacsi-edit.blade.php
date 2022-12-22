<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa thông tin bác sĩ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<style>
    .a2 {
        font-family: arial, sans-serif;
        border-collapse: collapse;
    }

    .a2 {
        position: relative;
        top: 60px;
        left: 270px;
        width: 1400px;
    }
</style>
<body>
@extends('admin-layout/menuadmin')
@section('content2')
    <div>
        <h2 style="position: relative; right: -270px; top: 15px">Sửa thông tin bác sĩ</h2>
        <button type="button" style="position: relative; right: -270px; top: 40px" class="btn btn-primary" onclick="window.location.href='{{URL::asset('admin/quanlybacsi/')}}';">Quay lại</button>

        <form method="POST" class="a2">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email</label>
                <input type="text" class="form-control" id="username" name="username" value="{{$accounts->username}}" aria-describedby="emailHelp" required>
                <div class="text">
                    @error('username')
                    <li style="color: red; font-size: 17px; padding-bottom: 5px">Tên đăng nhập bạn đăng kí đã tồn tại! Hoặc yêu cầu tối thiểu 4 kí tự và tối đa 30 kí tự </li>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" value="{{($accounts->password)}}" required>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Họ và tên</label>
                <input type="text" class="form-control" id="name" name="name" value="{{$accounts->name}}" required>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Số điện thoại</label>
                <input type="phone" class="form-control" id="phone" name="phone" value="{{$accounts->phones}}" required>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Ngày sinh</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{$accounts->date_of_births}}" required>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Giới tính</label>
                <select style="width: 1400px; height: 38px; padding-left: 10px" name="gender" id="gender" required>
                    <option value="Nam">Nam</option>
                    <option value="Nữ">Nữ</option>
                    <option value="Khác">Khác</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="address" name="address" value="{{$accounts->address}}" required>
            </div>
            <button type="submit" class=" btn btn-success" name="edit" id="edit"><i class="fa-solid fa-user-pen"></i>Sửa</button>
        </form>
    </div>
@endsection
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>

