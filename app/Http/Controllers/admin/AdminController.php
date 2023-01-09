<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\accounts;
use App\Models\appointment_schedules;
use App\Models\appointment_times;
use App\Models\health_checkup_packages;
use App\Models\payment_status;
use App\Models\rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    // Middleware
    function __construct()
    {
        $this->middleware('Check.Is.Admin');
    }

        // GET: http://localhost/Project2Final/admin/home
        // Trang home của admin
        function viewHome()
        {
            return view('admin-layout/dashboard_homepage/home');
        }

        function viewDoiMatKhau() {
            return view('admin-layout/Change_Password/doimatkhau');
        }

        function DoiMatKhau(Request $request)
        {
            $this->validate($request, [
                'password' => ['required', Password::min(10)->letters()->mixedCase()->symbols()],
                'confirm_password' => ['required', 'same:password'],
            ]);

            accounts::whereId(auth()->user()->id)->update([
            'password' => bcrypt($request->password)
            ]);
            return redirect('/admin/changepassword')->with("success", "Mật khẩu đã được đổi thành công");
        }

        function viewQuanLyKhachHang()
        {
            $accounts = accounts::where('isCustomer', '=', '1')
                ->where('status', '=', '0')
                ->get();
            return view('admin-layout/Quan_Ly_Khach_Hang/customer-all', ['accounts' => $accounts]);
        }

        function editKhach($id)
        {
            $accounts = accounts::where('id', '=', $id)->first();
            return view('admin-layout/Quan_Ly_Khach_Hang/customer-edit', compact('accounts'));
        }

        function updateKhach(RegisterRequest $request, $id)
        {
            $validated = $request->validated();
            if ($validated) {
                $accounts = accounts::findOrFail($id);
                $accounts->name = $request->name;
                $accounts->username = $request->username;
                $accounts->password = bcrypt($request->password);
                $accounts->save();
                return redirect('admin/quanlykhachhang/')->with('editDone', 'Cập nhật thông tin tài khoản khách hàng thành công!');
            }
        }

        function deleteKhach($id) {
            // Tìm đến đối tượng muốn xóa
            $accounts = accounts::findOrFail($id);
            $accounts->delete();
            return redirect('admin/quanlykhachhang/')->with('deleteDone', 'Xóa tài khoản thành công!');
        }

        function viewQuanLyKhachHang_KhoaTaiKhoan()
        {
            $accounts = accounts::where('isCustomer', '=', '1')
                ->where('status', '=', '1')
                ->get();
            return view('admin-layout/Quan_Ly_Khach_Hang/khoa_tai_khoan', ['accounts' => $accounts]);
        }

        function KhoaTaiKhoan_Khach(Request $request, $id) {
            $accounts = accounts::findOrFail($id);
            $accounts->status = 1;
            $accounts->save();
            return redirect('/admin/quanlykhachhang/lock')->with('success', 'Khóa tài khoản thành công');
        }

        function MoKhoaTaiKhoan_Khach(Request $request, $id) {
            $accounts = accounts::findOrFail($id);
            $accounts->status = 0;
            $accounts->save();
            return redirect('/admin/quanlykhachhang')->with('success', 'Mở khóa tài khoản thành công');
        }

        // GET: http://localhost/Project2Final/admin/quanlybacsi
        // Trang giao diện quản lý bác sĩ chung
        function viewQuanLyBacsi()
        {
            $bacsi = accounts::where('isDoctor', '=', '1')
                ->where('status' , '=', 0)
                ->get();
            return view('admin-layout/Quan_Ly_Bac_Si/bacsi-all', ['bacsi' => $bacsi]);
        }

        // GET: http://localhost/Project2Final/admin/quanlybacsi/add
        // Trang giao diện thêm bác sĩ
        function viewQuanLyBacsi_Add()
        {
            return view('admin-layout/Quan_Ly_Bac_Si/bacsi-add');
        }
        // Xử lý thêm bác sĩ
        function addbacsi(RegisterRequest $request)
        {
            $validated = $request->validated();
            if ($validated) {
                $accounts = new accounts();
                // syntax: $tên_biến -> tên_cột_trên_bảng = $request -> name(giá trị thẻ name trong html)
                $accounts->username = $request->username;
                $accounts->password = bcrypt($request->password);
                $accounts->name = $request->name;
                $accounts->phones = $request->phone;
                $accounts->date_of_births = $request->date_of_birth;
                $accounts->genders = $request->gender;
                $accounts->address = $request->address;
                $accounts->work_areas = $request->work_area;
                $accounts->doctorStatus = "0";
                $accounts->isDoctor = "1";
                $accounts->status = "0";
                $accounts->save();
                return redirect('admin/quanlybacsi/')->with('success', 'Thêm bác sĩ thành công!');
            }
        }
        // GET: http://localhost/Project2Final/admin/quanlybacsi/edit/{id}
        // Trang giao diện sửa bác sĩ
        function editDoctor(Request $request, $id)
        {
            $accounts = accounts::where('id', '=', $id)->first();
            return view('admin-layout/Quan_Ly_Bac_Si/bacsi-edit', compact('accounts'));
        }

        // POST: http://localhost/Project2Final/admin/quanlybacsi/edit/{id}
        // update thông tin bác sĩ
        function updateDoctor(RegisterRequest $request, $id)
        {
            $validated = $request->validated();
            if ($validated) {
                $accounts = accounts::findOrFail($id);
                $accounts->username = $request->username;
                $accounts->password = bcrypt($request->password);
                $accounts->name = $request->name;
                $accounts->phones = $request->phone;
                $accounts->date_of_births = $request->date_of_birth;
                $accounts->genders = $request->gender;
                $accounts->address = $request->address;
                $accounts->work_areas = $request->work_area;
                $accounts->save();
                return redirect('admin/quanlybacsi/')->with('editDone', 'Cập nhật thông tin bác sĩ thành công!');
            }
        }

        // POST: http://localhost/Project2Final/admin/quanlybacsi/delete/{id}
        // Trang giao diện xóa bác sĩ
        function deletedoctor($id) {
            // Tìm đến đối tượng muốn xóa
            $accounts = accounts::findOrFail($id);
            $accounts->delete();
            return redirect('admin/quanlybacsi/')->with('deleteDone', 'Xóa bác sĩ thành công!');
        }

        // trang giao diện hiển thị all gói khám + nút thêm, sửa, xóa
        function viewQuanLyGoiKham() {
            $goikham = health_checkup_packages::all();
            return view('admin-layout/Quan_Ly_Goi_Kham/goi-kham-all', ['goikham' => $goikham]);
        }

        // Trang giao diện thêm bác sĩ
        function viewQuanLyGoiKham_Add()
        {
            return view('admin-layout/Quan_Ly_Goi_Kham/goi-kham-add');
        }

        // Xử lý thêm bác sĩ
        function addGoiKham(Request $request)
        {
            $goikham = new health_checkup_packages();
            // syntax: $tên_biến -> tên_cột_trên_bảng = $request -> name(giá trị thẻ name trong html)
            $goikham->types = $request->type;
            $goikham->names = $request->name;
            $goikham->prices = $request->price;
            $goikham->descriptions = $request->description;
            $goikham->save();
            return redirect('admin/quanlygoikham/')->with('success', 'Thêm gói khám thành công!');
        }

        // Trang giao diện sửa bác sĩ
        function editGoiKham(Request $request, $id)
        {
            $goikham = health_checkup_packages::where('id', '=', $id)->first();
            return view('admin-layout/Quan_Ly_Goi_Kham/goi-kham-edit', compact('goikham'));
        }

        // update thông tin gói khám
        function updateGoiKham(Request $request, $id)
        {
            $goikham = health_checkup_packages::findOrFail($id);
            $goikham->types = $request->type;
            $goikham->names = $request->name;
            $goikham->prices = $request->price;
            $goikham->descriptions = $request->description;
            $goikham->save();
            return redirect('admin/quanlygoikham/')->with('editDone', 'Cập nhật thông tin gói khám thành công!');
        }

        // Xóa gói khám
        function deleteGoiKham($id) {
            $goikham = health_checkup_packages::findOrFail($id);
            $goikham->delete();
            return redirect('admin/quanlygoikham/')->with('deleteDone', 'Xóa gói khám thành công!');
        }

        // trang giao diện hiển thị all gói khám + nút thêm, sửa, xóa
        function viewQuanLyThoiGianHen() {
            $time = appointment_times::all();
            return view('admin-layout/Quan_Ly_Thoi_Gian_Hen/thoigian-hen-all', ['time' => $time]);
        }

        // Trang giao diện thêm bác sĩ
        function viewQuanLyThoiGianHen_Add()
        {
            return view('admin-layout/Quan_Ly_Thoi_Gian_Hen/thoigian-hen-add');
        }

        // Xử lý thêm bác sĩ
        function addThoiGianHen(Request $request)
        {
            $time = new appointment_times();
            // syntax: $variable -> column(db) = $request -> name(giá trị thẻ name trong html)
            $time->types = $request->type;
            $time->times = $request->time;
            $time->save();
            return redirect('admin/quanlythoigianhen/')->with('success', 'Thêm thời gian hẹn thành công!');
        }

        // Trang giao diện sửa bác sĩ
        function editThoiGianHen(Request $request, $id)
        {
            $time = appointment_times::where('id', '=', $id)->first();
            return view('admin-layout/Quan_Ly_Thoi_Gian_Hen/thoigian-hen-edit', compact('time'));
        }

        // update thông tin gói khám
        function updateThoiGianHen(Request $request, $id)
        {
            $time = appointment_times::findOrFail($id);
            $time->types = $request->type;
            $time->times = $request->time;
            $time->save();
            return redirect('admin/quanlythoigianhen/')->with('editDone', 'Cập nhật thông tin thời gian hẹn thành công!');
        }

        // Xóa gói khám
        function deleteThoiGianHen($id) {
            $time = appointment_times::findOrFail($id);
            $time->delete();
            return redirect('admin/quanlythoigianhen/')->with('deleteDone', 'Xóa mốc thời gian hẹn thành công!');
        }

        // trang giao diện hiển thị all phòng khám + nút thêm, sửa, xóa
        function viewThongTinPhongKham() {
            $room = rooms::all();
            return view('admin-layout/Quan_Ly_Phong_Kham/phongkham-all', ['room' => $room]);
        }

        // Trang giao diện thêm bác sĩ
        function viewThongTinPhongKham_Add()
        {
            return view('admin-layout/Quan_Ly_Phong_Kham/phongkham-add');
        }

        // Xử lý thêm phòng khám
        function addPhongKham(Request $request)
        {
            $room = new rooms();
            // syntax: $variable -> column(db) = $request -> name(giá trị thẻ name trong html)
            $room->types = $request->type;
            $room->rooms = $request->room;
            $room->isRoom = "0";
            $room->roomsStatus = "0";
            $room->save();
            return redirect('admin/quanlyphongkham/')->with('success', 'Thêm phòng khám thành công!');
        }

        // Trang giao diện sửa phòng khám
        function editPhongKham(Request $request, $id)
        {
            $room = rooms::where('id', '=', $id)->first();
            return view('admin-layout/Quan_Ly_Phong_Kham/phongkham-edit', compact('room'));
        }

        // update thông tin gói khám
        function updatePhongKham(Request $request, $id)
        {
            $room = rooms::findOrFail($id);
            $room->types = $request->type;
            $room->rooms = $request->room;
            $room->save();
            return redirect('admin/quanlyphongkham/')->with('editDone', 'Cập nhật thông tin phòng khám thành công!');
        }

        // Xóa gói khám
        function deletePhongKham($id) {
            $room = rooms::findOrFail($id);
            $room->delete();
            return redirect('admin/quanlyphongkham/')->with('deleteDone', 'Xóa phòng khám thành công!');
        }

        function viewQuanLyBacsi_KhoaTaiKhoan()
        {
            $bacsi = accounts::where('isDoctor', '=', '1')
                ->where('status', '=', '1')
                ->get();
            return view('admin-layout/Quan_Ly_Bac_Si/khoa_tai_khoan', ['bacsi' => $bacsi]);
        }

        function KhoaTaiKhoan_Bacsi(Request $request, $id) {
            $bacsi = accounts::findOrFail($id);
            $bacsi->status = 1;
            $bacsi->save();
            return redirect('/admin/quanlybacsi/lock')->with('success', 'Khóa tài khoản thành công');
        }

        function MoKhoaTaiKhoan_Bacsi(Request $request, $id) {
            $bacsi = accounts::findOrFail($id);
            $bacsi->status = 0;
            $bacsi->save();
            return redirect('/admin/quanlybacsi')->with('success', 'Mở khóa tài khoản thành công');
        }

        function viewLichHenChuaThanhToan()
        {
            $appointments = appointment_schedules::where('payment_status', '=', '0')
                ->where('status', '=', '1')
                ->get();

            // Convert the dates field to a Carbon instance
            $appointments->transform(function ($appointment) {
                $appointment->dates = Carbon::parse($appointment->dates);
                return $appointment;
            });

            // Group the appointments by the dates field
            $appointmentsByDate = $appointments->groupBy(function ($appointment) {
                return $appointment->dates->format('d-m-Y');
            });

            // Pass the grouped appointments to the view
            return view('admin-layout/Quan_Ly_Lich_Hen_XacNhan_ThanhToan/chua_thanh_toan', ['appointments' => $appointmentsByDate]);

            //return view('admin-layout/Quan_Ly_Lich_Hen_XacNhan_ThanhToan/chua_thanh_toan', ['appointments' => $appointmentsByDate]);
        }

        // Trang thêm lịch hẹn (tự chọn tgian cho khách chưa đặt lịch) ở trang lịch hẹn chưa xác nhân
        function viewLichHen_Add() {

            // Gói các gói khám có cùng types và hiển thị trên select
            $health_checkup_packages = health_checkup_packages::all();
            $grouped_packages = $health_checkup_packages->groupBy('types');

            // Gói các mốc thời gian có cùng types (buổi) và hiển thị trên select
            $appointment_times = appointment_times::all();
            $grouped_packages_times = $appointment_times->groupBy('types');

            // Gói các phòng có cùng types (kiểu phòng) và hiển thị trên select
            $rooms = rooms::all();
            $grouped_packages_rooms = $rooms->groupBy('types');

            $doctors = accounts::where('isDoctor', '=', '1')->get();

            return view('admin-layout/Quan_Ly_Lich_Hen_XacNhan_ThanhToan/lichhen-add', compact( 'doctors', 'grouped_packages', 'grouped_packages_times', 'grouped_packages_rooms'));
        }

        // Xử lý thêm lịch
        function addLichHen1(Request $request) {
            // Kiểm tra ngày, thời gian đã đc chọn quá 5 lần hay chưa
            $selectedTime = appointment_schedules::where('dates', $request->date)
                ->where('times', $request->time)
                ->first();

            $count = appointment_schedules::where('dates', $request->date)
                ->where('times', $request->time)
                ->count();

            // Nếu lịch hẹn có ngày và tgian trùng nhau quá 5 lần hiện tbao
            if ($selectedTime && $count > 4) {
                return redirect()->back()->with('errorDatLich', 'Thời gian đặt lịch này đã quá nhiều người đặt! Vui lòng chọn ngày hoặc mốc thời gian khác');
            }

            $appointment_schedules = new appointment_schedules;
            $appointment_schedules->accounts_id = Auth::id();
            $appointment_schedules->names = $request->name;
            $appointment_schedules->phones = $request->phone;
            $appointment_schedules->dates = $request->date;
            $appointment_schedules->times = $request->time;
            $appointment_schedules->prices = $request->price;
            $appointment_schedules->doctor_examines = $request->doctor_examine;
            $appointment_schedules->rooms = $request->room;

            $appointment_schedules->payment_status = $request->payment_status=0;
            $appointment_schedules->appointment_status = $request->appointment_status=0;
            $appointment_schedules->status = $request->status=0;
            $appointment_schedules->save();
            return redirect('/admin/lichhenchuaxacnhan')->with('success2', 'Đặt lịch thành công!');
        }

        // Trang sửa lịch hen
        function editLichHen(Request $request, $id)
        {
            $appointment_schedule = appointment_schedules::where('id', '=', $id)->first();

            $doctors = accounts::where('isDoctor', '=', '1')->get();

            // Gói các gói khám có cùng types và hiển thị trên select
            $health_checkup_packages = health_checkup_packages::all();
            $grouped_packages = $health_checkup_packages->groupBy('types');

            // Gói các mốc thời gian có cùng types (buổi) và hiển thị trên select
            $appointment_times = appointment_times::all();
            $grouped_packages_times = $appointment_times->groupBy('types');

            // Gói các phòng có cùng types (kiểu phòng) và hiển thị trên select
            $rooms = rooms::all();
            $grouped_packages_rooms = $rooms->groupBy('types');

            return view('admin-layout/Quan_Ly_Lich_Hen_XacNhan_ThanhToan/chua_xac_nhan-edit', compact('appointment_schedule', 'doctors', 'grouped_packages', 'grouped_packages_times', 'grouped_packages_rooms'));
        }

        // POST: http://localhost/Project2Final/admin/quanlylichhen/edit/{id}
        // update thông tin lịch hẹn
        function updateLichHen(Request $request, $id)
        {
            /*$selected = appointment_schedules::where('dates', $request->date)
                ->where('times', $request->time)
                ->orWhere(function($query) use ($request) {
                    $query->where('doctor_examines', $request->doctor_examine)
                        ->where('rooms', '!=', $request->doctor_examine);
                })
                ->orWhere(function($query) use ($request) {
                    $query->where('doctor_examines', '!=', $request->room)
                        ->where('rooms', $request->room);
                })
                ->first();*/

            // exclude the current appointment
            $selected = appointment_schedules::where('id', '!=', $id)
            ->where(function($query) use ($request) {
                $query->where('dates', $request->date)
                    ->where('times', $request->time)
                    ->where(function($query) use ($request) {
                        $query->where('doctor_examines', $request->doctor_examine)
                            ->orWhere('rooms', $request->doctor_examine);
                    });
            })
                ->orWhere(function($query) use ($request) {
                    $query->where('dates', $request->date)
                        ->where('times', $request->time)
                        ->where(function($query) use ($request) {
                            $query->where('doctor_examines', $request->room)
                                ->orWhere('rooms', $request->room);
                        });
                })
                ->first();

            if ($selected) {
                return redirect()->back()->with('errorSuaLich', 'Bác sĩ hoặc phòng khám bạn đã được chọn hoặc đang được sử dụng!');
            } else {
                $appointment_schedule = appointment_schedules::findOrFail($id);
                $appointment_schedule->names = $request->name;
                $appointment_schedule->phones = $request->phone;
                $appointment_schedule->dates = $request->date;
                $appointment_schedule->times = $request->time;
                $appointment_schedule->prices = $request->price;
                $appointment_schedule->doctor_examines = $request->doctor_examine;
                $appointment_schedule->rooms = $request->room;
                $appointment_schedule->save();
                return redirect('admin/lichhenchuaxacnhan')->with('editDone', 'Cập nhật thông tin lịch hẹn thành công!');
            }
        }

        // GET: http://localhost/Project2Final/admin/quanlylichhen/delete/{id}
        // Trang giao diện xóa bác sĩ
        function deleteLichHen($id) {
            // Tìm đến đối tượng muốn xóa
            $accounts = appointment_schedules::findOrFail($id);
            $accounts->delete();
            return redirect('admin/lichhenchuaxacnhan/')->with('deleteDone', 'Xóa lịch hẹn thành công!');
        }

        function viewLichHenChuaXacNhan()
        {
            // $lich_chua_xac_nhan = appointment_schedules::where('status', '=', '0')->get();
            // return view('admin-layout/Quan_Ly_Lich_Hen_XacNhan_ThanhToan/chua_xac_nhan', ['lich_chua_xac_nhan' => $lich_chua_xac_nhan]);

            $appointments = appointment_schedules::where('status', '=', '0')->get();

            // Convert the dates field to a Carbon instance
            $appointments->transform(function ($appointment) {
                $appointment->dates = Carbon::parse($appointment->dates);
                return $appointment;
            });

            // Group the appointments by the dates field
            $appointmentsByDate = $appointments->groupBy(function ($appointment) {
                return $appointment->dates->format('d-m-Y');
            });

            // Pass the grouped appointments to the view
            return view('admin-layout/Quan_Ly_Lich_Hen_XacNhan_ThanhToan/chua_xac_nhan', ['appointments' => $appointmentsByDate]);
        }

        function LichHenChuaXacNhan_sang_LichHenDaXacNhan(Request $request, $id) {
            $appointment_schedules = appointment_schedules::findOrFail($id);
            $appointment_schedules->status = 1;
            $appointment_schedules->save();
            return redirect('/admin/lichhendaxacnhan')->with('success', 'Lịch hẹn đã xác nhận thành công');
        }

        function LichHenDaXacNhan_sang_LichHenChuaXacNhan(Request $request, $id) {
            $appointment_schedules = appointment_schedules::findOrFail($id);
            $appointment_schedules->status = 0;
            $appointment_schedules->save();
            return redirect('/admin/lichhenchuaxacnhan')->with('success1', 'Hủy xác nhận lịch hẹn thành công');
        }

        function viewLichHenDaXacNhan()
        {
            //$lich_da_xac_nhan = appointment_schedules::where('status', '=', '1')->get();
            //return view('admin-layout/Quan_Ly_Lich_Hen_XacNhan_ThanhToan/da_xac_nhan', ['lich_da_xac_nhan' => $lich_da_xac_nhan]);

            $appointments = appointment_schedules::where('status', '=', '1')->get();

            // Convert the dates field to a Carbon instance
            $appointments->transform(function ($appointment) {
                $appointment->dates = Carbon::parse($appointment->dates);
                return $appointment;
            });

            // Group the appointments by the dates field
            $appointmentsByDate = $appointments->groupBy(function ($appointment) {
                return $appointment->dates->format('d-m-Y');
            });

            return view('admin-layout/Quan_Ly_Lich_Hen_XacNhan_ThanhToan/da_xac_nhan', ['appointments' => $appointmentsByDate]);
        }

        // GET: http://localhost/Project2Final/admin/lichhendathanhtoan
        // Trang lịch hẹn đã thanh toán
        function viewLichHenDaThanhToan()
        {
            $appointments = appointment_schedules::where('payment_status', '=', '1')
                ->where('status', '=', '1')
                ->get();

            // Convert the dates field to a Carbon instance
            $appointments->transform(function ($appointment) {
                $appointment->dates = Carbon::parse($appointment->dates);
                return $appointment;
            });

            // Group the appointments by the dates field
            $appointmentsByDate = $appointments->groupBy(function ($appointment) {
                return $appointment->dates->format('d-m-Y');
            });

            // Pass the grouped appointments to the view
            return view('admin-layout/Quan_Ly_Lich_Hen_XacNhan_ThanhToan/da_thanh_toan', ['appointments' => $appointmentsByDate]);

            //return view('admin-layout/Quan_Ly_Lich_Hen_XacNhan_ThanhToan/da_thanh_toan', ['lich_da_thanh_toan' => $lich_da_thanh_toan]);
        }

        function TrangThaiLichHen_sang_DaThanhToan(Request $request, $id) {
            $appointment_schedules = appointment_schedules::findOrFail($id);
            $appointment_schedules->payment_status = 1;
            $appointment_schedules->save();
            return redirect('/admin/lichhendathanhtoan')->with('success', 'Chuyển về lịch hẹn đã thanh toán thành công');
        }

        function DaThanhToan_sang_ChuaThanhToan(Request $request, $id) {
            $appointment_schedules = appointment_schedules::findOrFail($id);
            $appointment_schedules->payment_status = 0;
            $appointment_schedules->save();
            return redirect('/admin/lichhenchuathanhtoan')->with('success', 'Chuyển về lịch hẹn chưa thanh toán thành công');
        }
}
