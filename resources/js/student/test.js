document.addEventListener("DOMContentLoaded", function () {
    const questions = window.questions || [];
    const testData = window.testData || {};

    // Default translations as fallback
    const defaultTranslations = {
        timeUpTitle: "Time is up!",
        timeUpText: "Test is being completed automatically...",
        warningTitle: "Warning!",
        selectAnswer: "Please select an answer!",
        errorTitle: "Error!",
        errorOccurred: "An error occurred",
        serverError: "Could not connect to server",
        noFinishRoute:
            "Error: Test completion route not found. Please reload the page.",
        correctAnswer: "Correct answer!",
        wrongAnswer: "Wrong answer",
        finishTitle: "Finish Test",
        finishText: "Do you want to finish the test?",
        yesFinish: "Yes, finish",
        cancel: "Cancel",
        testFinished: "Test finished!",
        answeredQuestions: "Answered questions",
        correctAnswers: "Correct answers",
        wrongAnswers: "Wrong answers",
        timeUsed: "Time used",
    };

    // Get translations from blade or use defaults
    const translations = window.translations || defaultTranslations;

    function t(key) {
        // First check if window.translations exists and has the key
        if (
            window.translations &&
            typeof window.translations[key] !== "undefined"
        ) {
            return window.translations[key];
        }
        // Then check local translations object
        if (translations && typeof translations[key] !== "undefined") {
            return translations[key];
        }
        // Finally use default translations or return the key itself
        return defaultTranslations[key] || key;
    }

    let currentQuestionId = questions.length > 0 ? questions[0].id : 0;
    let currentQuestionIndex = 0; // Yangi: hozirgi savol indeksi
    let selectedAnswers = {};
    let completedQuestions = 0;
    let answeredQuestions = new Set(); // Javob berilgan savollar ro'yxati

    let totalTimeInSeconds =
        typeof testData.timeLimit !== "undefined" &&
        !isNaN(Number(testData.timeLimit)) &&
        Number(testData.timeLimit) > 0
            ? Number(testData.timeLimit)
            : 15 * 60;
    let remainingTime = totalTimeInSeconds;
    let timerInterval;
    let isTimerActive = true;
    let testFinished = false;

    function startTimer() {
        const timerDisplay = document.getElementById("timerDisplay");
        const progressCircle = document.getElementById("progressCircle");
        if (!timerDisplay) return;

        let circumference = 0;
        if (progressCircle) {
            if (typeof progressCircle.getTotalLength === "function") {
                circumference = progressCircle.getTotalLength();
            } else {
                circumference = 2 * Math.PI * 35;
            }
            progressCircle.style.strokeDasharray = `${circumference} ${circumference}   `;
            progressCircle.style.strokeDashoffset = 0;
            progressCircle.style.transition = "stroke-dashoffset 1s linear";
        }

        timerDisplay.textContent = formatTime(remainingTime);

        timerInterval = setInterval(function () {
            if (!isTimerActive || testFinished) return;

            remainingTime--;

            timerDisplay.textContent = formatTime(remainingTime);

            if (progressCircle && circumference > 0) {
                const progress =
                    (totalTimeInSeconds - remainingTime) / totalTimeInSeconds;
                const offset = circumference * progress;
                progressCircle.style.strokeDashoffset = offset;

                if (remainingTime <= 300) {
                    progressCircle.style.stroke = "#ef4444";
                    timerDisplay.style.color = "#ef4444";
                } else if (remainingTime <= 600) {
                    progressCircle.style.stroke = "#f59e0b";
                    timerDisplay.style.color = "#f59e0b";
                }
            }

            if (remainingTime <= 0) {
                clearInterval(timerInterval);
                timeUp();
            }
        }, 1000);
    }

    function formatTime(time) {
        const minutes = Math.floor(time / 60);
        let seconds = time % 60;
        if (seconds < 10) seconds = "0" + seconds;
        return `${minutes}:${seconds}`;
    }

    function timeUp() {
        if (testFinished) return;
        isTimerActive = false;
        testFinished = true;

        // Barcha savollarning javob variantlarini bloklash
        document.querySelectorAll(".variant-card").forEach((card) => {
            card.style.pointerEvents = "none";
            card.classList.add("disabled");
        });
        document
            .querySelectorAll(".submit-btn")
            .forEach((btn) => (btn.disabled = true));

        Swal.fire({
            title: t("timeUpTitle"),
            text: t("timeUpText"),
            icon: "warning",
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false,
        }).then(() => finishTest(true));
    }

    // Navigation tugmalari uchun event listener
    function setupNavigationButtons() {
        // Previous button
        const prevBtn = document.getElementById("prevQuestionBtn");
        if (prevBtn) {
            prevBtn.addEventListener("click", function () {
                if (currentQuestionIndex > 0 && !testFinished) {
                    currentQuestionIndex--;
                    const prevQuestionId = questions[currentQuestionIndex].id;
                    showQuestion(prevQuestionId, currentQuestionIndex + 1);
                }
            });
        }

        // Next button
        const nextBtn = document.getElementById("nextQuestionBtn");
        if (nextBtn) {
            nextBtn.addEventListener("click", function () {
                if (
                    currentQuestionIndex < questions.length - 1 &&
                    !testFinished
                ) {
                    currentQuestionIndex++;
                    const nextQuestionId = questions[currentQuestionIndex].id;
                    showQuestion(nextQuestionId, currentQuestionIndex + 1);
                }
            });
        }

        // Question navigation buttons (1, 2, 3, ...)
        document.querySelectorAll(".nav-btn").forEach(function (btn) {
            btn.addEventListener("click", function () {
                if (testFinished) return; // Test tugaganda navigation ishlamasin

                const questionId = parseInt(this.dataset.questionId, 10);
                const questionIndex = questions.findIndex(
                    (q) => q.id === questionId
                );
                if (questionIndex !== -1) {
                    currentQuestionIndex = questionIndex;
                    showQuestion(questionId, questionIndex + 1);
                }
            });
        });
    }

    document.querySelectorAll(".variant-card").forEach(function (card) {
        card.addEventListener("click", function (e) {
            if (!isTimerActive || testFinished) return;
            e.preventDefault();
            e.stopPropagation();

            const answerId = parseInt(this.dataset.answerId, 10);
            const questionId = parseInt(this.dataset.questionId, 10);
            const questionContainer = document.getElementById(
                `question-${questionId}`
            );
            if (!questionContainer) return;

            const allVariants =
                questionContainer.querySelectorAll(".variant-card");
            allVariants.forEach((variant) =>
                variant.classList.remove("selected")
            );
            this.classList.add("selected");

            const radioInput = questionContainer.querySelector(
                `input[value="${answerId}"]`
            );
            if (radioInput) {
                questionContainer
                    .querySelectorAll('input[type="radio"]')
                    .forEach((radio) => (radio.checked = false));
                radioInput.checked = true;
            }

            selectedAnswers[questionId] = answerId;
            const submitBtn = document.getElementById(`submitBtn${questionId}`);
            if (submitBtn) submitBtn.disabled = false;

            this.style.animation = "pulse 0.6s ease-in-out";
        });
    });

    document.querySelectorAll(".submit-btn").forEach(function (btn) {
        btn.addEventListener("click", function (e) {
            if (!isTimerActive || testFinished) return;
            e.preventDefault();

            const questionId = parseInt(this.dataset.questionId, 10);
            const selectedAnswerId = selectedAnswers[questionId];

            if (!selectedAnswerId) {
                Swal.fire({
                    title: t("warningTitle"),
                    text: t("selectAnswer"),
                    icon: "warning",
                    confirmButtonText: "OK",
                });
                return;
            }

            if (testData.routes && testData.routes.submitAnswer) {
                submitAnswerToServer(questionId, selectedAnswerId, this);
            } else {
                handleAnswerLocally(questionId, selectedAnswerId, this);
            }
        });
    });

    function submitAnswerToServer(questionId, answerId, submitBtn) {
        fetch(testData.routes.submitAnswer, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": testData.csrfToken,
            },
            body: JSON.stringify({
                session_id: testData.sessionId,
                question_id: questionId,
                answer_id: answerId,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    handleAnswerResult(questionId, data.is_correct, submitBtn);
                } else {
                    Swal.fire({
                        title: t("errorTitle"),
                        text: data.message || t("errorOccurred"),
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                }
            })
            .catch(() => handleAnswerLocally(questionId, answerId, submitBtn));
    }

    function handleAnswerLocally(questionId, answerId, submitBtn) {
        const questionContainer = document.getElementById(
            `question-${questionId}`
        );
        const selectedCard = questionContainer
            ? questionContainer.querySelector(`[data-answer-id="${answerId}"]`)
            : null;
        const isCorrect =
            selectedCard && selectedCard.dataset.isCorrect === "true";
        handleAnswerResult(questionId, isCorrect, submitBtn);
    }

    function handleAnswerResult(questionId, isCorrect, submitBtn) {
        if (testFinished) return;

        // Javob berilgan savolni belgilash
        answeredQuestions.add(questionId);

        const navBtn = document.getElementById(`navBtn${questionId}`);
        if (navBtn) {
            navBtn.classList.remove("current");
            if (isCorrect) {
                navBtn.classList.remove("incorrect");
                navBtn.classList.add("correct");
            } else {
                navBtn.classList.remove("correct");
                navBtn.classList.add("incorrect");
            }
            if (!navBtn.dataset.completed) {
                completedQuestions++;
                navBtn.dataset.completed = "true";
                updateProgress();
            }
        }

        // Faqat joriy savol uchun variant-cardlarni bloklash
        const questionContainer = document.getElementById(
            `question-${questionId}`
        );
        if (questionContainer) {
            questionContainer
                .querySelectorAll(".variant-card")
                .forEach((variant) => {
                    variant.style.pointerEvents = "none";
                    variant.classList.add("disabled");
                });
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = isCorrect
                ? `<i class="icofont icofont-check me-2"></i>${t(
                      "correctAnswer"
                  )}`
                : `<i class="icofont icofont-close me-2"></i>${t(
                      "wrongAnswer"
                  )}`;
        }

        setTimeout(() => {
            if (completedQuestions >= questions.length) {
                clearInterval(timerInterval);
                isTimerActive = false;
                finishTest();
            } else if (currentQuestionIndex < questions.length - 1) {
                // Avtomatik keyingi savolga o'tish
                currentQuestionIndex++;
                const nextQuestionId = questions[currentQuestionIndex].id;
                showQuestion(nextQuestionId, currentQuestionIndex + 1);
            }
        }, 1500);
    }

    function updateProgress() {
        const completedElement = document.getElementById("completedCount");
        const progressBar = document.getElementById("progressBar");

        if (completedElement) completedElement.textContent = completedQuestions;
        if (progressBar) {
            const percentage =
                questions.length > 0
                    ? (completedQuestions / questions.length) * 100
                    : 0;
            progressBar.style.width = percentage + "%";
            progressBar.setAttribute("aria-valuenow", completedQuestions);
        }
    }

    function setupFinishButton() {
        // Class selector yoki id selector bilan finish tugmasini topish
        const finishBtn = document.querySelector(".btn-danger, #finishTestBtn");
        if (finishBtn) finishBtn.addEventListener("click", handleFinishClick);
        return finishBtn;
    }

    function handleFinishClick(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        Swal.fire({
            title: t("finishTitle"),
            text: t("finishText"),
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: t("yesFinish"),
            cancelButtonText: t("cancel"),
        }).then((result) => {
            if (result.isConfirmed) finishTest(false);
        });
    }

    function showQuestion(questionId, questionNumber) {
        if (testFinished) return;

        // Hamma savollarni yashirish
        document
            .querySelectorAll(".question-container")
            .forEach((q) => q.classList.remove("active"));

        // Ko'rsatiladigan savolni faollashtirish
        const targetQuestion = document.getElementById(
            `question-${questionId}`
        );
        if (targetQuestion) targetQuestion.classList.add("active");

        // Navigation tugmalarni yangilash
        document
            .querySelectorAll(".nav-btn")
            .forEach((btn) => btn.classList.remove("current"));

        const navBtn = document.getElementById(`navBtn${questionId}`);
        if (navBtn) {
            // Agar javob berilmagan bo'lsa, current qilish
            if (
                !navBtn.classList.contains("correct") &&
                !navBtn.classList.contains("incorrect")
            ) {
                navBtn.classList.add("current");
            }
        }

        // Global o'zgaruvchilarni yangilash
        currentQuestionId = questionId;

        // Question number ni yangilash
        const currentQuestionElement =
            document.getElementById("currentQuestion");
        if (currentQuestionElement) {
            currentQuestionElement.textContent = questionNumber;
        }

        updateNavigationButtons();

        if (selectedAnswers[questionId]) {
            const selectedAnswerId = selectedAnswers[questionId];
            const questionContainer = document.getElementById(
                `question-${questionId}`
            );
            if (questionContainer) {
                const selectedCard = questionContainer.querySelector(
                    `[data-answer-id="${selectedAnswerId}"]`
                );
                if (selectedCard) {
                    selectedCard.classList.add("selected");
                    const radioInput = questionContainer.querySelector(
                        `input[value="${selectedAnswerId}"]`
                    );
                    if (radioInput) radioInput.checked = true;
                }
            }
        }
    }

    function updateNavigationButtons() {
        const prevBtn = document.getElementById("prevQuestionBtn");
        const nextBtn = document.getElementById("nextQuestionBtn");

        if (prevBtn) {
            prevBtn.disabled = currentQuestionIndex === 0 || testFinished;
            prevBtn.style.display =
                currentQuestionIndex === 0 ? "none" : "inline-block";
        }

        if (nextBtn) {
            nextBtn.disabled =
                currentQuestionIndex === questions.length - 1 || testFinished;
            nextBtn.style.display =
                currentQuestionIndex === questions.length - 1
                    ? "none"
                    : "inline-block";
        }
    }

    function finishTest(isAutomatic = false) {
        if (testFinished && !isAutomatic) return;
        if (!isAutomatic && !testFinished) testFinished = true;

        clearInterval(timerInterval);
        isTimerActive = false;

        document.querySelectorAll(".nav-btn").forEach((btn) => {
            btn.disabled = true;
            btn.style.pointerEvents = "none";
        });

        if (!testData || !testData.routes || !testData.routes.finish) {
            Swal.fire({
                title: t("errorTitle"),
                text: t("noFinishRoute"),
                icon: "error",
                confirmButtonText: "OK",
            }).then(() => showLocalResults());
            return;
        }

        fetch(testData.routes.finish, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": testData.csrfToken || "",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({}),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    if (data.redirect_url)
                        window.location.href = data.redirect_url;
                    else showLocalResults();
                } else {
                    Swal.fire({
                        title: t("errorTitle"),
                        text: data.message || t("errorOccurred"),
                        icon: "error",
                        confirmButtonText: "OK",
                    }).then(() => showLocalResults());
                }
            })
            .catch(() => {
                Swal.fire({
                    title: t("errorTitle"),
                    text: t("serverError"),
                    icon: "error",
                    confirmButtonText: "OK",
                }).then(() => showLocalResults());
            });
    }

    function showLocalResults() {
        let totalAnswered = Object.keys(selectedAnswers || {}).length;
        let correctCount = 0;
        let incorrectCount = 0;

        for (let questionId in selectedAnswers || {}) {
            const answerId = selectedAnswers[questionId];
            const selectedCard = document.querySelector(
                `[data-question-id="${questionId}"][data-answer-id="${answerId}"]`
            );
            if (selectedCard && selectedCard.dataset.isCorrect === "true")
                correctCount++;
            else incorrectCount++;
        }

        const usedMinutes = Math.floor(
            (totalTimeInSeconds - remainingTime) / 60
        );
        const usedSeconds = (totalTimeInSeconds - remainingTime) % 60;

        Swal.fire({
            title: t("testFinished"),
            html: `
                <div class="text-start">
                    <p><strong>${t(
                        "answeredQuestions"
                    )}:</strong> ${totalAnswered}</p>
                    <p><strong>${t(
                        "correctAnswers"
                    )}:</strong> ${correctCount}</p>
                    <p><strong>${t(
                        "wrongAnswers"
                    )}:</strong> ${incorrectCount}</p>
                    <p><strong>${t(
                        "timeUsed"
                    )}:</strong> ${usedMinutes}:${usedSeconds
                .toString()
                .padStart(2, "0")}</p>
                </div>
            `,
            icon: "info",
            confirmButtonText: "OK",
            allowOutsideClick: false,
        }).then(() => {
            window.location.href = "/student/";
        });
    }

    window.showQuestion = showQuestion;
    window.finishTest = finishTest;

    startTimer();
    if (questions.length > 0) {
        showQuestion(questions[0].id, 1);
    }

    setTimeout(() => {
        setupFinishButton();
        setupNavigationButtons();
        updateNavigationButtons();
    }, 500);
});
