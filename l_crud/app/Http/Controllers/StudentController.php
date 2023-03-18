<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Phương thức gốc (mặc định) của lớp StudentController
     */
    public function index()
    {
        //
        $data = Student::latest()->paginate(5);

        return view('index', compact('data'))->with('i', (request()->input('page', 1) - 1) * 5);

    }

    /**
     * Phương thức được sử dụng để tải Hiển thị biểu mẫu Thêm sinh viên trong trình duyệt
     */
    public function create()
    {
        //
        return view('create');
    }

    /**
     * Phương thức được sử dụng để xử lý Thêm yêu cầu dữ liệu sinh viên
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'student_name'          =>  'required',
            'student_email'         =>  'required|email|unique:students',
            'student_image'         =>  'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000'
        ]);

        $file_name = time() . '.' . request()->student_image->getClientOriginalExtension();

        request()->student_image->move(public_path('images'), $file_name);

        $student = new Student;

        $student->student_name = $request->student_name;
        $student->student_email = $request->student_email;
        $student->student_gender = $request->student_gender;
        $student->student_image = $file_name;

        $student->save();

        return redirect()->route('students.index')->with('success', 'Student Added successfully.');

    }

    /**
     * Phương thức được sử dụng để hiển thị dữ liệu của một sinh viên trên trang web
     */
    public function show(Student $student)
    {
        //
        return view('show', compact('student'));
    }

    /**
     * Phương thức được sử dụng để tải biểu mẫu sinh viên chỉnh sửa trong trình duyệt
     */
    public function edit(Student $student)
    {
        //
        return view('edit', compact('student'));
    }

    /**
     * Phương thức xử lý yêu cầu cập nhật dữ liệu của sinh viên
     */
    public function update(Request $request, Student $student)
    {
        //
        $request->validate([
            'student_name'      =>  'required',
            'student_email'     =>  'required|email',
            'student_image'     =>  'image|mimes:jpg,png,jpeg,gif,svg|max:2048|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000'
        ]);

        $student_image = $request->hidden_student_image;

        if($request->student_image != '')
        {
            $student_image = time() . '.' . request()->student_image->getClientOriginalExtension();

            request()->student_image->move(public_path('images'), $student_image);
        }

        $student = Student::find($request->hidden_id);

        $student->student_name = $request->student_name;

        $student->student_email = $request->student_email;

        $student->student_gender = $request->student_gender;

        $student->student_image = $student_image;

        $student->save();

        return redirect()->route('students.index')->with('success', 'Student Data has been updated successfully');

    }

    /**
     * Phương pháp được sử dụng để xóa dữ liệu sinh viên khỏi cơ sở dữ liệu.
     */
    public function destroy(Student $student)
    {
        //
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student Data deleted successfully');

    }
}
