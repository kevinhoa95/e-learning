<?php

namespace App\Http\Controllers;

use App\Answers;
use App\Http\Controllers\Auth\AuthController;
use App\Questions;
use App\Posts;
use App\Spaces;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\PostsController;

class QuestionsController extends Controller
{
	protected static $imageQuestionPath = '/public/images/imageQuestion/';

	public function viewQuestion($QuestionID){
		$Question = Questions::find($QuestionID);
		if (count($Question) < 1){
			return view('errors.404');
		}
		$Question = $Question->toArray();
		$format = Posts::find($Question['PostID'])->FormatID;
		if ($format == 1){ // Multiple-choice Question
			$Answers = Answers::where('QuestionID', '=', $QuestionID)->get()->toArray();
			return view('viewquestion')->with(compact('Question', 'Answers'));
		}
		else if ($format == 2){ // Filled Question
			$Answers = array();
			$Spaces = Spaces::where('QuestionID', '=', $QuestionID)->get()->toArray();
			foreach ($Spaces as $value) {
				$Answers += array($value['id'] => Answers::where('SpaceID', '=', $value['id'])->get()->toArray());
			}
			// dd($Answers);
			return view('admin.viewfilledquestion')->with(compact('Question', 'Spaces', 'Answers'));
		}
		
	}

	public function addQuestion($PostID){
		if (!AuthController::checkPermission()){
			return redirect('auth/login');
		};
		$post = Posts::find($PostID);

		return view(($post['FormatID'] == 1) ? 'admin.addquestion' : 'admin.addfilledquestion')->with(['PostID' => $PostID]);
	}

	public function saveQuestion($PostID){
		if (!AuthController::checkPermission()){
			return redirect('/');
		}
		$data = Request::capture();
		$question = new Questions();
		$question->PostID = $PostID;
		$question->ThumbnailID = $data['ThumbnailID'];
		$question->Question = $data['Question'];
		$question->Description = $data['Description'];
		switch ($data['ThumbnailID']){
			case '1': // Photo
				$question->save();
				$question = Questions::orderBy('id', 'desc')->first();

				//Photo
				$file = Request::capture()->file('Photo');
				if ($file != null){
					$question->Photo = 'Question_' . $PostID . '_' . $question->id . "_-Evangels-English-www.evangelsenglish.com_" . "." . $file->getClientOriginalExtension();
					$file->move(base_path() . '/public/images/imageQuestion/', $question->Photo);
				}

				$question->update();
				break;
			case '2': // Video
				$linkVideo = $data['Video'];
				$question->Video = PostsController::getYoutubeVideoID($linkVideo);
				$question->save();
				break;
		}
		echo $question->id;
		return;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if (!AuthController::checkPermission()){
			return redirect('/');
		}
		$Question = Questions::find($id);
		$format = Posts::find($Question['PostID'])['FormatID'];
		// dd($format);
		switch ($format) {
			case 1:			// Multiple-choices Question
				return view('admin.editquestion', compact('Question'));
				break;
			case 2:			// Filled Question
				$Spaces = Spaces::where('QuestionID', '=', $Question['id'])->get()->toArray();
				$rawAnswers = array();
				foreach ($Spaces as $key => $value) {
					$ra = '';
					$Answers = Answers::where('SpaceID', '=', $value['id'])->get()->toArray();
					foreach ($Answers as $key => $v) {
						$ra .= $v['Detail'] . "; ";
					}
					$rawAnswers = array_merge($rawAnswers, [$ra]);
				}
				return view('admin.editfilledquestion', compact('Question', 'rawAnswers'));
				break;
		}
		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if (!AuthController::checkPermission()){
			return redirect('/');
		}
		$data = $request->all();
		$question = Questions::find($id);
		$question->Question = $data['Question'];
		$question->ThumbnailID = $data['ThumbnailID'];
		$question->Description = $data['Description'];
		$question->update();

		switch ($data['ThumbnailID']){
			case '1': // Photo
				// if admin upload new photo
				if ($request->file('Photo') != null) {
					$question = Questions::find($id);

					$file = $request->file('Photo');
					$question->Photo = 'Question_' . $question['PostID'] . '_' . $question->id . "_-Evangels-English-www.evangelsenglish.com_" . "." . $file->getClientOriginalExtension();
					$file->move(base_path() . '/public/images/imageQuestion/', $question->Photo);

					$question->update();
				}
				break;
			case '2':
				$question->Video = PostsController::getYoutubeVideoID($data['Video']);
				$question->update();
		}
		$format = Posts::find($question->PostID)['FormatID'];
		if ($format == 1)
			return redirect(route('user.viewquestion', $question->id));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public static function destroy($id)
	{
		if (!AuthController::checkPermission()){
			return redirect('/');
		}
		$question = Questions::find($id);
		@unlink(public_path('images/imageQuestion/' . $question['Photo']));
		$postid = $question['PostID'];
		$format = Posts::find($postid)['FormatID'];
		if ($format == 1){
			$answers = Answers::where('QuestionID', '=', $id)->get()->toArray();
			foreach ($answers as $answer) {
				Answers::destroy($answer['id']);
			}
		}
		else if ($format == 2){
			$spaces = Spaces::where('QuestionID', '=', $id)->get()->toArray();
			foreach ($spaces as $value) {
				SpacesController::destroy($value['id']);
			}
		}
		$question->delete();
		return redirect(route('user.viewpost', $postid));
	}
}
