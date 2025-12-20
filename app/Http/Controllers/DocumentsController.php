<?php

namespace App\Http\Controllers;

use App\Models\Documents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentsController extends Controller
{

    public function uploadStudentDocs(Request $request)
    {

        if (count($request->file()) == 0) {
            return response()->json(['message' => 'Upload Atleast One file!'], 422);
        }
        $documentCategories = $request->input('document_category');

        $rules = [
            'student_id' => 'required|exists:students,id',
            'document_category.*' =>  'nullable|string',
        ];
        foreach ($documentCategories as $documentCategory) {
            if ($documentCategory == '18') {
                // $rules['file_' . $documentCategory] = 'nullable|file|mimes:mp3,mp4|max:8192';
                $rules['file_' . $documentCategory] = 'nullable|file|mimes:mp4,mp3,m4a,opus,ogg,3gp|max:10192';
            } else {
                $rules['file_' . $documentCategory] = 'nullable|file|mimes:jpg,png,jpeg,pdf|max:2048';
            }
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Log::info('Validation Rules:', $rules);

        $filePaths = [];
        $studentId = $request->input('student_id');
        foreach ($documentCategories as $documentCategory) {
            $file = $request->file('file_' . $documentCategory);

            if ($file) {
                $existingDocument = Documents::where('student_id', $studentId)
                    ->where('doc_category_id', $documentCategory)
                    ->first();

                if ($existingDocument) {
                    // Unlink (delete) the existing file if it exists
                    $existingFilePath = $existingDocument->document_path;
                    if (Storage::disk('public')->exists($existingFilePath)) {
                        Storage::disk('public')->delete($existingFilePath);
                    }
                }
                $fileName = "{$documentCategory}_{$studentId}_" . $file->getClientOriginalName();
                $filePath = $file->storeAs('student_docs', $fileName, 'public');
                $filePaths[$documentCategory] =  asset('storage/' . $filePath);


                Documents::updateOrCreate(
                    ['student_id' => $studentId, 'doc_category_id' => $documentCategory],
                    ['document_path' => $filePath,  'uploaded_by' => Auth::id(), 'status' => 'approved']
                );
            }
        }
        $this->update_profile_completion($studentId, 2, 3);
        $prolfile_completed = $this->getProfileCompletedState($studentId);
        return response()->json([
            'message' => '<i class="fa fa-check-circle text-success">&nbsp;</i>Files uploaded successfully!',
            'file_paths' => $filePaths,
            'prolfile_completed' => $prolfile_completed
        ], 200);
    }
}
