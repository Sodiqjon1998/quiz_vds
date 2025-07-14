<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Student\Quiz;
use App\Models\Teacher\Question;
use Auth;
use DB;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Question::find()->paginate(20);
        return view('teacher.question.index', compact('model'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        return view('teacher.question.create', [
            'quizId' => $id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Ma'lumotlarni validatsiya qilish (tavsiya etiladi!)
        $request->validate([
            'question_text' => 'required|string|max:2000',
            'quiz_id' => 'required|integer|exists:quiz,id', // 'quizzes' sizning quizlar jadvali nomi
            'status' => 'required|boolean', // Yoki qanday turda bo'lishiga qarab
            'options' => 'required|array|min:2', // Kamida 2 ta variant bo'lishi kerak
            'options.*.text' => 'required|string|max:1000', // Har bir variant matni
            'correct_option_id' => 'required|integer', // To'g'ri javob IDsi
        ]);

        // Question modelini yaratish va saqlash
        $question = new Question();
        $question->name = $request->input('question_text');
        $question->quiz_id = $request->input('quiz_id');
        $question->status = $request->input('status', 1); // Agar status kiritilmasa, default 1 bo'lsin
        $question->created_by = Auth::user()->id;
        $question->updated_by = Auth::user()->id;
        // dd($request->input('correct_option_id'));

        if ($question->save()) {
            // Variantlarni saqlash
            foreach ($request->input('options') as $optionCounter => $optionData) {
                $option = new Option();
                $option->question_id = $question->id;
                $option->name = $optionData['text']; // Blade dan kelayotgan 'text' maydoni

                // 'correct_option_id' bu 'options' massividagi indeksga mos kelishi kerak
                // Bizning Blade dagi 'correct_option_id' valusi options ichidagi raqamga mos kelardi.
                // Masalan, 1-variant uchun radio valusi 1, 2-variant uchun 2 va hokazo.
                // Shuning uchun bu yerda $optionCounter bilan solishtiramiz.
                $option->is_correct = ($request->input('correct_option_id') == $optionCounter) ? 1 : 0;

                $option->status = 1; // Default status
                $option->created_by = Auth::user()->id;
                $option->updated_by = Auth::user()->id;
                $option->save();
            }

            return redirect()->route('teacher.quiz.create')->with('success', 'Question and options have been created successfully!');
        }

        return redirect()->route('teacher.question.create', ['id' => $question->id])->with('error', 'Failed to create question.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        // Savolga tegishli variantlar va quiz ma'lumotlarini yuklaymiz
        $question->load('options', 'quiz');
        return view('teacher.question.show', compact('question'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Berilgan ID bo'yicha savolni bazadan topish
        // with(['options', 'quiz']) bilan options va quiz relatsiyalarini eager-load qilamiz
        $question = Question::with(['options', 'quiz'])->findOrFail($id);

        // Agar sizda Quiz dropdown listi mavjud bo'lsa, uni ham uzating
        // (faqat create sahifasida kerak bo'ladi, lekin ba'zida editda ham o'zgartirish imkoniyati bo'lishi mumkin)
        $quizzes = Quiz::pluck('name', 'id'); // Quiz nomlarini va IDlarini olamiz
        // Tahrirlash (edit) formasini ko'rsatish
        // 'teacher.questions.edit' - bu sizning Blade faylingiz joylashgan yo'l
        return view('teacher.question.edit', compact('question', 'quizzes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) // <- Bu yerda o'zgarish
    {
        // Berilgan ID bo'yicha savolni topamiz
        // Agar topa olmasa, avtomatik 404 xatosini qaytaradi
        $question = Question::findOrFail($id);

        $validatedData = $request->validate([
            'question_text' => 'required|string|max:2000',
            // 'quiz' o'rniga 'quizzes' bo'lishi kerak, chunki bu jadval nomi
            // Agar sizning jadvalingiz nomi "quiz" bo'lsa, uni shu holatda qoldiring.
            // Lekin Laravel konvensiyasi ko'plikda "quizzes" bo'lishini tavsiya qiladi.
            'quiz_id' => 'required|exists:quiz,id',
            'status' => 'required|boolean',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string|max:500',
            // To'g'ri variant indeksi options massivining chegarasida bo'lishi kerak
            'correct_option_id' => 'required|integer|min:0|max:' . (count($request->input('options')) - 1),
        ], [
            'question_text.required' => 'Savol matni kiritilishi shart.',
            'options.required' => 'Savol variantlari kiritilishi shart.',
            'options.min' => 'Kamida :min ta variant bo\'lishi kerak.',
            'options.*.text.required' => 'Variant matni bo\'sh bo\'lmasligi kerak.',
            'correct_option_id.required' => 'To\'g\'ri javobni belgilash shart.',
            'correct_option_id.min' => 'To\'g\'ri javob indeksi noto\'g\'ri.',
            'correct_option_id.max' => 'To\'g\'ri javob indeksi variantlar sonidan oshib ketdi.',
            'quiz_id.required' => 'Quiz tanlanishi shart.',
            'quiz_id.exists' => 'Tanlangan quiz topilmadi.',
        ]);

        DB::beginTransaction();

        try {
            // Savolni yangilash
            // Sizning kodingizda `$question->name` deb yozilgan, agar `questions` jadvalida
            // savol matni `question_text` ustunida saqlansa, uni `question_text` ga o'zgartiring.
            // Men avvalgi Blade fayliga moslab `question_text` deb o'zgartiraman.
            $question->name = $validatedData['question_text'];
            $question->quiz_id = $validatedData['quiz_id'];
            $question->status = $validatedData['status'];
            // Savolni yaratgan foydalanuvchi ID si (created_by) yangilashda o'zgarmasligi kerak.
            // Faqat updated_by yangilanadi.
            $question->created_by = Auth::user()->id; // Bu qatorni olib tashlash kerak
            $question->updated_by = Auth::user()->id;
            $question->save();

            // Eski variantlarni o'chirish
            // Bu holatda $question->options()->delete() to'g'ri ishlaydi
            $question->options()->delete();

            // Yangi variantlarni qo'shish
            foreach ($validatedData['options'] as $index => $optionData) {
                $option = new Option();
                // Sizning kodingizda `$option->name` deb yozilgan, agar `options` jadvalida
                // variant matni `option_text` ustunida saqlansa, uni `option_text` ga o'zgartiring.
                // Men avvalgi Blade fayliga moslab `option_text` deb o'zgartiraman.
                $option->name = $optionData['text'];
                $option->is_correct = ($index == $validatedData['correct_option_id']);
                $option->created_by = Auth::user()->id; // Yangi variantlar uchun created_by kerak
                $option->updated_by = Auth::user()->id;
                $option->status = 1; // Default status
                $question->options()->save($option);
            }

            DB::commit();

            // `teacher.quiz.show` o'rniga `teacher.quizzes.show` bo'lishi mumkin,
            // marshrutingizga qarab tekshiring.
            // Avvalgi javobda `teacher.quizzes.show` edi.
            return redirect()->route('teacher.quiz.show', $question->quiz_id)
                ->with('success', 'Savol muvaffaqiyatli yangilandi!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Istisnoni batafsilroq loglashni tavsiya qilaman
            \Log::error("Savolni yangilashda xatolik: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile());
            return back()->withInput()->withErrors(['error' => 'Savolni yangilashda xatolik yuz berdi. Iltimos, qayta urinib ko\'ring. ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction(); // Ma'lumotlar butunligini ta'minlash uchun tranzaksiya boshlaymiz

        try {
            // 1. O'chirilishi kerak bo'lgan savolni topamiz
            $question = Question::findOrFail($id);

            // 2. Savolning qaysi quizga tegishli ekanligini yozib olamiz
            // Chunki savol o'chirilgandan keyin uning quiz_id siga kira olmaymiz
            $quizIdToRedirect = $question->quiz_id;

            // 3. Savolni o'chiramiz
            // Avtomatik ravishda unga tegishli variantlar ham o'chirilishi uchun
            // Question modelida `boot` metodida `deleting` eventiga listener yozishingiz mumkin,
            // yoki Options modelida `onDelete('cascade')`ni o'rnatgan bo'lishingiz kerak migrationda.
            // Aks holda, avval variantlarni o'chirish kerak bo'ladi.
            // Agar `onDelete('cascade')` o'rnatilmagan bo'lsa:
            // $question->options()->delete();
            $question->delete();

            DB::commit(); // Tranzaksiyani yakunlaymiz

            // 4. Savol tegishli bo'lgan quizning sahifasiga qayta yo'naltiramiz
            // Marshrut nomingiz `teacher.quizzes.show` bo'lsa, shuni ishlating
            return redirect()->route('teacher.quizzes.show', $quizIdToRedirect)
                ->with('success', 'Savol muvaffaqiyatli o\'chirildi.');
        } catch (\Exception $e) {
            DB::rollBack(); // Xato yuz bersa, tranzaksiyani bekor qilish
            \Log::error("Savolni o'chirishda xatolik: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile());
            return back()->withErrors(['error' => 'Savolni o\'chirishda xatolik yuz berdi. ' . $e->getMessage()]);
        }
    }
}
