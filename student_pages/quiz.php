<?php
/*
 * KIDSS - Quiz Page
 * Location: student_pages/quiz.php
 *
 * Media folder expected:
 * assets/
 *   quiz/
 *     images/
 *       A.jpg, B.jpg, C.jpg, D.jpg, E.jpg, F.jpg, G.jpg, H.jpg
 *     audio/
 *       A.mp3, B.mp3, C.mp3, D.mp3, E.mp3, F.mp3, G.mp3, H.mp3
 *     videos/
 *       A.mp4, B.mp4, C.mp4, D.mp4, E.mp4, F.mp4, G.mp4, H.mp4
 *
 * You can replace the file names below with your actual media.
 */

$quizItems = [];
foreach (range('A', 'Z') as $letter) {
    $imagePath = 'assets/quiz/images/' . $letter . '.png';
    $audioPath = 'assets/quiz/audio/' . $letter . '.mp3';
    $videoPath = 'assets/quiz/videos/' . $letter . '.mp4';

    // Build the quiz from the image files included with the current project.
    if (is_file(__DIR__ . '/../' . $imagePath)) {
        $quizItems[] = [
            'answer' => $letter,
            'image' => $imagePath,
            'audio' => $audioPath,
            'audioAvailable' => is_file(__DIR__ . '/../' . $audioPath),
            'video' => $videoPath,
            'videoAvailable' => is_file(__DIR__ . '/../' . $videoPath),
            'label' => 'Letter ' . $letter,
        ];
    }
}

shuffle($quizItems);
?>

<style>
/* =========================================================
   KIDSS QUIZ
   Self-contained styles because quiz.php is dynamically loaded.
   ========================================================= */

.quiz-page {
    width: 100%;
    max-width: 1180px;
    margin: 0 auto;
    padding-bottom: 30px;
    color: #12265d;
}

.quiz-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 22px;
}

.quiz-title-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
}

.quiz-title-icon {
    width: 70px;
    height: 70px;
    border-radius: 20px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #e8ddff, #dbeafe);
    color: #7048e8;
    font-size: 2rem;
    box-shadow: 0 8px 20px rgba(112,72,232,.12);
}

.quiz-header h1 {
    margin: 0;
    font-size: clamp(1.8rem, 3vw, 2.5rem);
    font-weight: 900;
}

.quiz-header p {
    margin: 5px 0 0;
    color: #66728e;
    font-weight: 600;
}

.quiz-score {
    min-width: 125px;
    padding: 12px 18px;
    border-radius: 18px;
    background: #fff;
    border: 1px solid #edf0f6;
    box-shadow: 0 7px 20px rgba(40,55,100,.06);
    text-align: center;
}

.quiz-score small {
    display: block;
    color: #71809e;
    font-size: .72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.quiz-score strong {
    display: block;
    margin-top: 2px;
    color: #7048e8;
    font-size: 1.35rem;
}

.quiz-progress {
    height: 9px;
    margin-bottom: 22px;
    overflow: hidden;
    border-radius: 20px;
    background: #e9e7f3;
}

.quiz-progress-fill {
    width: 0;
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, #7048e8, #4c9cff);
    transition: width .3s ease;
}

.quiz-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
    gap: 22px;
}

.quiz-card {
    background: #fff;
    border: 1px solid #edf0f6;
    border-radius: 24px;
    box-shadow: 0 9px 25px rgba(40,55,100,.06);
    padding: 22px;
}

.question-label {
    color: #7048e8;
    font-size: .78rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .7px;
    margin-bottom: 12px;
}

.question-image-wrap {
    width: 100%;
    height: 330px;
    border-radius: 20px;
    background: #f7f8fc;
    display: grid;
    place-items: center;
    overflow: hidden;
    border: 2px dashed #e1e5ef;
}

.question-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 15px;
}

.question-fallback {
    display: none;
    text-align: center;
    color: #7048e8;
    font-weight: 900;
    font-size: 5rem;
}

.question-prompt {
    margin: 18px 0 0;
    font-size: 1.15rem;
    font-weight: 800;
    color: #24365e;
    text-align: center;
}

.media-tools {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 18px;
}

.media-btn {
    min-height: 54px;
    border: 0;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    font-weight: 900;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
}

.media-btn:hover {
    transform: translateY(-2px);
}

.audio-btn {
    background: #fff4cf;
    color: #9a6900;
}

.video-btn {
    background: #e8f3ff;
    color: #126bc1;
}

.media-btn i {
    font-size: 1.25rem;
}

.answers-title {
    margin-bottom: 13px;
    color: #24365e;
    font-size: 1rem;
    font-weight: 900;
}

.answers-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.answer-btn {
    min-height: 70px;
    border: 2px solid #e9edf5;
    border-radius: 17px;
    background: #fff;
    color: #24365e;
    font-size: 1.05rem;
    font-weight: 900;
    cursor: pointer;
    text-align: left;
    padding: 12px 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: .15s ease;
}

.answer-btn:hover:not(:disabled) {
    border-color: #8e70e8;
    background: #faf8ff;
    transform: translateY(-2px);
}

.answer-letter {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    background: #f0ebff;
    color: #7048e8;
    font-size: .85rem;
}

.answer-btn.correct {
    border-color: #34a853;
    background: #ecf9f0;
    color: #20783a;
}

.answer-btn.correct .answer-letter {
    background: #34a853;
    color: #fff;
}

.answer-btn.wrong {
    border-color: #ef6262;
    background: #fff0f0;
    color: #a83333;
}

.answer-btn.wrong .answer-letter {
    background: #ef6262;
    color: #fff;
}

.answer-btn:disabled {
    cursor: default;
}

.feedback-box {
    min-height: 55px;
    margin-top: 17px;
    padding: 13px 15px;
    border-radius: 15px;
    display: none;
    align-items: center;
    gap: 10px;
    font-weight: 800;
}

.feedback-box.show {
    display: flex;
}

.feedback-box.correct-feedback {
    background: #ecf9f0;
    color: #237b3d;
}

.feedback-box.wrong-feedback {
    background: #fff0f0;
    color: #a83333;
}

.next-btn,
.restart-btn {
    width: 100%;
    margin-top: 15px;
    min-height: 54px;
    border: 0;
    border-radius: 16px;
    background: #7048e8;
    color: #fff;
    font-size: 1rem;
    font-weight: 900;
    cursor: pointer;
    box-shadow: 0 8px 18px rgba(112,72,232,.2);
}

.next-btn:hover,
.restart-btn:hover {
    background: #5f3dc4;
}

.next-btn:disabled {
    opacity: .45;
    cursor: not-allowed;
}

.video-panel {
    margin-top: 20px;
    display: none;
}

.video-panel.show {
    display: block;
}

.video-notice { margin: 10px 0; padding: 12px; border-radius: 12px; background: #fff4cf; color: #795600; font-size: .9rem; font-weight: 700; }

.video-panel-title {
    margin-bottom: 9px;
    font-size: .85rem;
    font-weight: 900;
    color: #526181;
}

.quiz-video {
    width: 100%;
    max-height: 290px;
    border-radius: 18px;
    background: #101522;
    display: block;
}

.quiz-complete {
    display: none;
    text-align: center;
    padding: 35px 20px;
}

.quiz-complete.show {
    display: block;
}

.complete-icon {
    width: 88px;
    height: 88px;
    margin: 0 auto 15px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background: #ecf9f0;
    color: #2da653;
    font-size: 2.7rem;
}

.quiz-complete h2 {
    margin: 0 0 7px;
    font-size: 1.8rem;
    font-weight: 900;
}

.quiz-complete p {
    color: #65718c;
    font-weight: 600;
}

.final-score {
    margin: 18px auto;
    font-size: 2.5rem;
    font-weight: 950;
    color: #7048e8;
}

@media (max-width: 900px) {
    .quiz-layout {
        grid-template-columns: 1fr;
    }

    .question-image-wrap {
        height: 300px;
    }
}

@media (max-width: 600px) {
    .quiz-header {
        align-items: flex-start;
    }

    .quiz-score {
        min-width: 95px;
    }

    .quiz-title-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
    }

    .quiz-card {
        padding: 16px;
    }

    .question-image-wrap {
        height: 240px;
    }

    .answers-grid,
    .media-tools {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="quiz-page">

    <div class="quiz-header">
        <div class="quiz-title-wrap">
            <div class="quiz-title-icon">
                <i class="bi bi-puzzle-fill" aria-hidden="true"></i>
            </div>
            <div>
                <h1>Sign Language Quiz</h1>
                <p>Look at the picture and choose the correct answer!</p>
            </div>
        </div>

        <div class="quiz-score" aria-live="polite">
            <small>Score</small>
            <strong id="quizScore">0 / <?= count($quizItems) ?></strong>
        </div>
    </div>

    <div class="quiz-progress" aria-label="Quiz progress">
        <div class="quiz-progress-fill" id="quizProgress"></div>
    </div>

    <div id="quizGame">

        <div class="quiz-layout">

            <!-- Question / Visual Learning -->
            <section class="quiz-card">
                <div class="question-label" id="questionNumber">
                    Question 1 of <?= count($quizItems) ?>
                </div>

                <div class="question-image-wrap">
                    <img
                        id="questionImage"
                        class="question-image"
                        src=""
                        alt="ASL sign question"
                        onerror="this.style.display='none'; document.getElementById('questionFallback').style.display='block';"
                    >
                    <div id="questionFallback" class="question-fallback" aria-hidden="true"></div>
                </div>

                <p class="question-prompt">
                    Which letter is shown in the picture?
                </p>

                <!-- Sound is optional support, while video provides visual signing. -->
                <div class="media-tools">
                    <button type="button" class="media-btn audio-btn" id="audioButton">
                        <i class="bi bi-volume-up-fill" aria-hidden="true"></i>
                        <span>Play Sound</span>
                    </button>

                    <button type="button" class="media-btn video-btn" id="videoButton">
                        <i class="bi bi-play-circle-fill" aria-hidden="true"></i>
                        <span>Watch Sign</span>
                    </button>
                </div>

                <audio id="questionAudio" preload="metadata"></audio>

                <div class="video-panel" id="videoPanel">
                    <div class="video-panel-title">Sign-language video</div>
                    <p id="videoNotice" class="video-notice" role="status" hidden></p>
                    <video
                        id="questionVideo"
                        class="quiz-video"
                        controls
                        playsinline
                        preload="metadata"
                    >
                        Your browser does not support video playback.
                    </video>
                </div>
            </section>

            <!-- Answers -->
            <section class="quiz-card">
                <div id="questionArea">
                    <div class="answers-title">
                        Choose one answer:
                    </div>

                    <div class="answers-grid" id="answersGrid"></div>

                    <div
                        id="feedbackBox"
                        class="feedback-box"
                        role="status"
                        aria-live="polite"
                    ></div>

                    <button
                        type="button"
                        class="next-btn"
                        id="nextButton"
                        disabled
                    >
                        Next Question
                        <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                    </button>
                </div>

                <div id="quizComplete" class="quiz-complete">
                    <div class="complete-icon">
                        <i class="bi bi-trophy-fill" aria-hidden="true"></i>
                    </div>

                    <h2>Quiz Complete!</h2>
                    <p>Great job! You finished the sign-language quiz.</p>

                    <div class="final-score" id="finalScore">0 / 0</div>

                    <button type="button" class="restart-btn" id="restartButton">
                        <i class="bi bi-arrow-repeat me-2" aria-hidden="true"></i>
                        Try Again
                    </button>
                </div>
            </section>

        </div>

    </div>
</div>

<script>
(function () {
    /*
     * The PHP array is safely passed to JavaScript.
     * Choices are randomized for every question.
     */
    const quizItems = <?= json_encode(
        $quizItems,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ) ?>;

    let currentQuestion = 0;
    let score = 0;
    let answered = false;

    const questionImage = document.getElementById('questionImage');
    const questionFallback = document.getElementById('questionFallback');
    const questionAudio = document.getElementById('questionAudio');
    const questionVideo = document.getElementById('questionVideo');

    const answersGrid = document.getElementById('answersGrid');
    const feedbackBox = document.getElementById('feedbackBox');
    const nextButton = document.getElementById('nextButton');

    const quizScore = document.getElementById('quizScore');
    const quizProgress = document.getElementById('quizProgress');
    const questionNumber = document.getElementById('questionNumber');

    const audioButton = document.getElementById('audioButton');
    const videoButton = document.getElementById('videoButton');
    const videoPanel = document.getElementById('videoPanel');

    const quizGame = document.getElementById('quizGame');
    const questionArea = document.getElementById('questionArea');
    const quizComplete = document.getElementById('quizComplete');
    const finalScore = document.getElementById('finalScore');
    const restartButton = document.getElementById('restartButton');

    function shuffle(array) {
        /*
         * Fisher-Yates shuffle gives a new random order.
         */
        const copy = [...array];

        for (let i = copy.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [copy[i], copy[j]] = [copy[j], copy[i]];
        }

        return copy;
    }

    function getChoices(correctAnswer) {
        const allLetters = quizItems.map(item => item.answer);

        /*
         * Pick three incorrect choices, then add the correct answer.
         * The final four choices are shuffled.
         */
        const wrongChoices = shuffle(
            allLetters.filter(letter => letter !== correctAnswer)
        ).slice(0, 3);

        return shuffle([correctAnswer, ...wrongChoices]);
    }

    function loadQuestion() {
        answered = false;

        const item = quizItems[currentQuestion];
        const choices = getChoices(item.answer);

        questionNumber.textContent =
            `Question ${currentQuestion + 1} of ${quizItems.length}`;

        quizProgress.style.width =
            `${((currentQuestion) / quizItems.length) * 100}%`;

        quizScore.textContent = `${score} / ${quizItems.length}`;

        /*
         * Reset image fallback.
         */
        questionImage.style.display = 'block';
        questionFallback.style.display = 'none';
        questionFallback.textContent = item.answer;

        questionImage.src = item.image;
        questionImage.alt = `Picture showing the ASL sign for ${item.label}`;

        /*
         * Prepare audio.
         */
        questionAudio.pause();
        questionAudio.currentTime = 0;
        questionAudio.removeAttribute('src');
        if (item.audioAvailable) questionAudio.src = item.audio;

        // Video files are optional in this project version. Explain when one
        // has not been supplied instead of leaving a broken player visible.
        questionVideo.pause();
        questionVideo.removeAttribute('src');
        if (item.videoAvailable) questionVideo.src = item.video;
        questionVideo.load();
        videoPanel.classList.remove('show');
        const videoNotice = document.getElementById('videoNotice');
        videoNotice.hidden = true;
        videoNotice.textContent = '';

        /*
         * Reset feedback and next button.
         */
        feedbackBox.className = 'feedback-box';
        feedbackBox.innerHTML = '';
        nextButton.disabled = true;

        /*
         * Build randomized choices.
         */
        answersGrid.innerHTML = '';

        choices.forEach((choice, index) => {
            const button = document.createElement('button');

            button.type = 'button';
            button.className = 'answer-btn';
            button.dataset.answer = choice;

            button.innerHTML = `
                <span class="answer-letter">${String.fromCharCode(65 + index)}</span>
                <span>Letter ${choice}</span>
            `;

            button.addEventListener('click', function () {
                checkAnswer(choice, item.answer);
            });

            answersGrid.appendChild(button);
        });
    }

    function checkAnswer(selectedAnswer, correctAnswer) {
        if (answered) {
            return;
        }

        answered = true;

        const buttons = answersGrid.querySelectorAll('.answer-btn');

        buttons.forEach(button => {
            button.disabled = true;

            if (button.dataset.answer === correctAnswer) {
                button.classList.add('correct');
            }

            if (
                button.dataset.answer === selectedAnswer &&
                selectedAnswer !== correctAnswer
            ) {
                button.classList.add('wrong');
            }
        });

        if (selectedAnswer === correctAnswer) {
            score++;

            feedbackBox.className =
                'feedback-box show correct-feedback';

            feedbackBox.innerHTML = `
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                <span>Great job! That is the correct answer.</span>
            `;
        } else {
            feedbackBox.className =
                'feedback-box show wrong-feedback';

            feedbackBox.innerHTML = `
                <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                <span>The correct answer is Letter ${correctAnswer}.</span>
            `;
        }

        quizScore.textContent = `${score} / ${quizItems.length}`;
        nextButton.disabled = false;

        /*
         * Show full progress after the current question is answered.
         */
        quizProgress.style.width =
            `${((currentQuestion + 1) / quizItems.length) * 100}%`;
    }

    function nextQuestion() {
        if (!answered) {
            return;
        }

        currentQuestion++;

        if (currentQuestion >= quizItems.length) {
            showResults();
            return;
        }

        loadQuestion();
    }

    function showResults() {
        questionArea.style.display = 'none';
        quizComplete.classList.add('show');

        quizProgress.style.width = '100%';
        quizScore.textContent = `${score} / ${quizItems.length}`;
        finalScore.textContent = `${score} / ${quizItems.length}`;

        questionAudio.pause();
        questionVideo.pause();
    }

    function restartQuiz() {
        /*
         * Shuffle the questions again so a new attempt can have
         * a different question order.
         */
        shuffleInPlace(quizItems);

        currentQuestion = 0;
        score = 0;

        questionArea.style.display = 'block';
        quizComplete.classList.remove('show');

        loadQuestion();
    }

    function shuffleInPlace(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }

        return array;
    }

    audioButton.addEventListener('click', function () {
        const item = quizItems[currentQuestion];
        if (item.audioAvailable && questionAudio.src) {
            questionAudio.currentTime = 0;
            questionAudio.play().catch(function () {});
            return;
        }

        // Audio clips are not bundled yet; use the browser's speech voice as
        // a useful fallback for the letter name, where supported.
        if ('speechSynthesis' in window && 'SpeechSynthesisUtterance' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance('Letter ' + item.answer);
            utterance.rate = 0.85;
            window.speechSynthesis.speak(utterance);
        } else {
            const videoNotice = document.getElementById('videoNotice');
            videoPanel.classList.add('show');
            videoNotice.hidden = false;
            videoNotice.textContent = 'No audio file is installed for this letter, and speech playback is not supported by this browser.';
        }
    });

    videoButton.addEventListener('click', function () {
        const item = quizItems[currentQuestion];
        videoPanel.classList.add('show');
        const videoNotice = document.getElementById('videoNotice');
        if (!item.videoAvailable) {
            videoNotice.hidden = false;
            videoNotice.textContent = 'The sign demonstration video for Letter ' + item.answer + ' has not been added yet. Add a matching MP4 file under assets/quiz/videos/ to enable it.';
            questionVideo.removeAttribute('src');
            questionVideo.load();
            return;
        }
        videoNotice.hidden = true;
        questionVideo.play().catch(function () {});
    });

    nextButton.addEventListener('click', nextQuestion);
    restartButton.addEventListener('click', restartQuiz);

    /*
     * Start the first question.
     */
    loadQuestion();
})();
</script>