<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamRuleStoreRequest;
use App\Models\Exam;
use App\Models\ExamRule;
use Illuminate\Http\Request;
use App\Traits\SchoolSession;
use App\Repositories\ExamRuleRepository;
use App\Interfaces\SchoolSessionInterface;

class ExamRuleController extends Controller
{
    use SchoolSession;

    protected $schoolSessionRepository;

    public function __construct(SchoolSessionInterface $schoolSessionRepository)
    {
        $this->schoolSessionRepository = $schoolSessionRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loggedInUser = auth()->user();
        $current_school_session_id = $this->getSchoolCurrentSession();
        $exam_id = $request->query('exam_id', 0);
        $examRuleRepository = new ExamRuleRepository();
        $exam_rules = $examRuleRepository->getAll($current_school_session_id, $exam_id, $loggedInUser->school_id);

        $data = [
            'exam_rules' => $exam_rules
        ];
        return view('exams.view-rule', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $loggedInUser = auth()->user();
        $current_school_session_id = $this->getSchoolCurrentSession();
        $exam_id = $request->query('exam_id');

        if ($exam_id) {
            $exam = Exam::where('id', $exam_id)
                ->where('school_id', $loggedInUser->school_id)
                ->first();

            if (!$exam) {
                return abort(404);
            }
        }

        $data = [
            'exam_id' => $exam_id,
            'current_school_session_id' => $current_school_session_id,
        ];

        return view('exams.add-rule', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ExamRuleStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExamRuleStoreRequest $request)
    {
        try {
            $payload = $request->validated();
            $payload['school_id'] = auth()->user()->school_id;

            $examRuleRepository = new ExamRuleRepository();
            $examRuleRepository->create($payload);

            return back()->with('status', 'Exam rule creation was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExamRule  $examRule
     * @return \Illuminate\Http\Response
     */
    public function show(ExamRule $examRule)
    {
        return view('exams.view-rule');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $loggedInUser = auth()->user();
        $examRuleRepository = new ExamRuleRepository();
        $exam_rule = $examRuleRepository->getById($request->exam_rule_id, $loggedInUser->school_id);

        if (!$exam_rule) {
            return abort(404);
        }

        $data = [
            'exam_rule_id'  => $request->exam_rule_id,
            'exam_rule'     => $exam_rule,
        ];
        return view('exams.edit-rule', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $loggedInUser = auth()->user();
            $examRuleRepository = new ExamRuleRepository();
            $examRuleRepository->update($request, $loggedInUser->role === 'super_admin' ? null : $loggedInUser->school_id);

            return back()->with('status', 'Exam rule update was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExamRule  $examRule
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExamRule $examRule)
    {
        //
    }
}
