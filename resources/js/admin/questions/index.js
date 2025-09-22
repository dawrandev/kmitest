class QuestionsIndex {
    constructor() {
        this.searchTimeout = null;
        this.init();
    }

    init() {
        this.bindEvents();
        // Sahifa yuklanganda ham styling'ni qo'llash
        this.applyCSSStyles();
    }

    bindEvents() {
        // Language filter
        document.querySelectorAll('input[name="language"]').forEach((radio) => {
            radio.addEventListener("change", () => {
                if (radio.checked) {
                    this.filterQuestions();
                }
            });
        });

        // Search functionality
        const searchInput = document.getElementById("searchInput");
        const searchButton = document.getElementById("searchButton");

        if (searchInput) {
            searchInput.addEventListener("input", () => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.filterQuestions();
                }, 500);
            });

            searchInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    this.filterQuestions();
                }
            });
        }

        if (searchButton) {
            searchButton.addEventListener("click", () => {
                this.filterQuestions();
            });
        }

        // Event delegation - bu muhim qism
        this.bindDynamicEvents();
    }

    // Barcha dinamik elementlar uchun event delegation
    bindDynamicEvents() {
        // View question modal
        document.addEventListener("click", (e) => {
            if (e.target.closest(".view-question-btn")) {
                const button = e.target.closest(".view-question-btn");
                const questionId = button.dataset.questionId;

                // Current selected language
                const selectedLanguage = document.querySelector(
                    'input[name="language"]:checked'
                );
                const languageId = selectedLanguage
                    ? selectedLanguage.value
                    : null;

                if (!languageId) {
                    Swal.fire({
                        title: window.translations?.attention || "Diqqat!",
                        text:
                            window.translations?.selectLanguage ||
                            "Iltimos tilni tanlang",
                        icon: "warning",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                        buttonsStyling: false,
                    });
                    return;
                }

                this.loadQuestionDetails(questionId, languageId);
            }
        });

        // Delete question
        document.addEventListener("click", (e) => {
            if (e.target.closest(".delete-btn")) {
                const questionId =
                    e.target.closest(".delete-btn").dataset.questionId;
                this.confirmDelete(questionId);
            }
        });
    }

    filterQuestions() {
        const selectedLanguage = document.querySelector(
            'input[name="language"]:checked'
        )?.value;
        const searchQuery = document.getElementById("searchInput")?.value;

        const url = new URL(window.location.origin + window.location.pathname);

        if (selectedLanguage) {
            url.searchParams.set("language_id", selectedLanguage);
        }

        if (searchQuery) {
            url.searchParams.set("search", searchQuery);
        }

        // Show loading state
        const container = document.getElementById("questionsContainer");
        if (container) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">${
                            window.translations?.loading || "Yuklanmoqda..."
                        }</span>
                    </div>
                </div>
            `;
        }

        fetch(url.toString(), {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => response.text())
            .then((html) => {
                // Extract questions container from response
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = html;
                const newContainer = tempDiv.querySelector(
                    "#questionsContainer"
                );

                if (newContainer && container) {
                    container.innerHTML = newContainer.innerHTML;

                    // Add current selected language to new view buttons
                    const currentSelectedLanguage = document.querySelector(
                        'input[name="language"]:checked'
                    );
                    if (currentSelectedLanguage) {
                        const viewButtons =
                            container.querySelectorAll(".view-question-btn");
                        viewButtons.forEach((btn) => {
                            btn.setAttribute(
                                "data-language-id",
                                currentSelectedLanguage.value
                            );
                        });
                    }

                    // CSS styling'ni qayta qo'llash
                    this.applyCSSStyles();
                }

                // Update URL without page reload
                history.pushState(null, "", url.toString());
            })
            .catch((error) => {
                console.error("Error:", error);
                if (container) {
                    container.innerHTML = `
                        <div class="alert alert-danger text-center">
                            ${
                                window.translations?.errorLoading ||
                                "Savollarni yuklashda xatolik"
                            }
                        </div>
                    `;
                }
            });
    }

    // CSS styling'ni qayta qo'llash funksiyasi
    applyCSSStyles() {
        // Barcha card elementlarni qayta styling qilish
        const questionCards = document.querySelectorAll(".question-card");
        questionCards.forEach((card) => {
            // Hover effects va transition'larni qayta qo'llash
            card.style.transition = "all 0.3s ease";

            // Hover event listener'larni qayta qo'shish
            card.addEventListener("mouseenter", () => {
                card.style.transform = "translateY(-3px)";
                card.style.boxShadow = "0 8px 25px rgba(0, 0, 0, 0.15)";
            });

            card.addEventListener("mouseleave", () => {
                card.style.transform = "translateY(0)";
                card.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.1)";
            });
        });

        // Button hover effects
        const buttons = document.querySelectorAll(
            ".btn-outline-info, .btn-outline-warning, .btn-outline-danger"
        );
        buttons.forEach((btn) => {
            btn.addEventListener("mouseenter", () => {
                btn.style.transform = "translateY(-1px)";
            });

            btn.addEventListener("mouseleave", () => {
                btn.style.transform = "translateY(0)";
            });
        });
    }

    loadQuestionDetails(questionId, languageId) {
        const modal = new bootstrap.Modal(
            document.getElementById("questionModal")
        );
        const modalBody = document.getElementById("questionModalBody");

        if (modalBody) {
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">${
                            window.translations?.loading || "Yuklanmoqda..."
                        }</span>
                    </div>
                </div>
            `;
        }

        modal.show();

        fetch(`/admin/questions/${questionId}/show?language_id=${languageId}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    this.displayQuestionDetails(data.data, languageId);
                } else {
                    if (modalBody) {
                        modalBody.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                if (modalBody) {
                    modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            ${
                                window.translations?.errorLoadingDetails ||
                                "Savol tafsilotlarini yuklashda xatolik"
                            }
                        </div>
                    `;
                }
            });
    }

    displayQuestionDetails(question, languageId) {
        const modalBody = document.getElementById("questionModalBody");
        if (!modalBody) return;

        let html = "";

        // Display image at the top if exists
        if (question.translation && question.translation.image) {
            html += `
                <div class="text-center mb-4">
                    <img src="/storage/${question.translation.image}" 
                         class="img-fluid modal-question-image" 
                         alt="Question Image">
                </div>
            `;
        }

        // Question text
        html += `
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <h6 class="text-primary mb-0 me-2">
                        <i class="icofont icofont-question-circle me-2"></i>
                        ${window.translations?.question || "Savol"}
                    </h6>
                    <span class="badge bg-primary">${
                        question.translation
                            ? question.translation.language.name
                            : "N/A"
                    }</span>
                </div>
                <div class="p-3 bg-light rounded">
                    <p class="mb-0 fs-6 text-dark fw-semibold">${
                        question.translation
                            ? question.translation.text
                            : window.translations?.noTranslation ||
                              "Tarjima mavjud emas"
                    }</p>
                </div>
            </div>
        `;

        // Answers
        html += `
            <div>
                <h6 class="text-success mb-3">
                    <i class="icofont icofont-ui-check me-2"></i>
                    ${window.translations?.answerOptions || "Javob variantlari"}
                </h6>
        `;

        question.answers.forEach((answer, index) => {
            const answerText = answer.translation
                ? answer.translation.text
                : window.translations?.noTranslation || "Tarjima mavjud emas";
            const isCorrect = answer.is_correct;

            html += `
                <div class="answer-item mb-3 p-3 rounded ${
                    isCorrect
                        ? "bg-success bg-opacity-25 border-success"
                        : "bg-light border-secondary"
                }" style="border: 1px solid;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge ${
                            isCorrect ? "bg-success" : "bg-secondary"
                        }">
                            ${String.fromCharCode(65 + index)}
                        </span>
                        ${
                            isCorrect
                                ? '<i class="icofont icofont-check-circled text-success fs-5"></i>'
                                : ""
                        }
                    </div>
                    <p class="mb-0 text-dark">${answerText}</p>
                </div>
            `;
        });

        html += `</div>`;
        modalBody.innerHTML = html;
    }

    confirmDelete(questionId) {
        Swal.fire({
            title: window.translations?.deleteQuestion || "Savolni o'chirish",
            text:
                window.translations?.deleteConfirmText ||
                "Bu savolni o'chirishga ishonchingiz komilmi? Bu amal qaytarib bo'lmaydi!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText:
                window.translations?.confirmDelete || "Ha, o'chirish!",
            cancelButtonText: window.translations?.cancel || "Bekor qilish",
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                actions: "d-flex gap-3 justify-content-center",
                confirmButton: "btn btn-danger me-2",
                cancelButton: "btn btn-secondary",
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                this.deleteQuestion(questionId);
            }
        });
    }

    deleteQuestion(questionId) {
        Swal.fire({
            title: window.translations?.deleting || "O'chirilmoqda...",
            text: window.translations?.pleaseWait || "Iltimos, kuting",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        fetch(`/admin/questions/${questionId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    Swal.fire({
                        title: window.translations?.deleted || "O'chirildi!",
                        text:
                            window.translations?.deleteSuccess ||
                            "Savol muvaffaqiyatli o'chirildi.",
                        icon: "success",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-success",
                        },
                        buttonsStyling: false,
                    }).then(() => {
                        // Remove the card from DOM
                        const card = document
                            .querySelector(`[data-question-id="${questionId}"]`)
                            ?.closest(".col-lg-3");
                        if (card) {
                            card.remove();
                        }

                        // Check if no questions left
                        const questionsContainer =
                            document.getElementById("questionsContainer");
                        const questionCards =
                            questionsContainer?.querySelectorAll(
                                ".question-card"
                            );

                        if (!questionCards || questionCards.length === 0) {
                            location.reload(); // Reload to show "No questions found" message
                        }
                    });
                } else {
                    Swal.fire({
                        title: window.translations?.error || "Xatolik!",
                        text:
                            data.message ||
                            window.translations?.deleteError ||
                            "Savolni o'chirishda xatolik yuz berdi.",
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                        buttonsStyling: false,
                    });
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                Swal.fire({
                    title: window.translations?.error || "Xatolik!",
                    text:
                        window.translations?.serverError ||
                        "Server bilan bog'lanishda xatolik yuz berdi.",
                    icon: "error",
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    buttonsStyling: false,
                });
            });
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new QuestionsIndex();
});
