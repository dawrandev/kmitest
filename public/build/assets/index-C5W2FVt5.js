class f{constructor(){this.searchTimeout=null,this.init()}init(){this.bindEvents(),this.applyCSSStyles()}bindEvents(){document.querySelectorAll('input[name="language"]').forEach(t=>{t.addEventListener("change",()=>{t.checked&&this.filterQuestions()})});const e=document.getElementById("searchInput"),i=document.getElementById("searchButton");e&&(e.addEventListener("input",()=>{clearTimeout(this.searchTimeout),this.searchTimeout=setTimeout(()=>{this.filterQuestions()},500)}),e.addEventListener("keypress",t=>{t.key==="Enter"&&(t.preventDefault(),this.filterQuestions())})),i&&i.addEventListener("click",()=>{this.filterQuestions()}),this.bindDynamicEvents()}bindDynamicEvents(){document.addEventListener("click",e=>{var i,t;if(e.target.closest(".view-question-btn")){const o=e.target.closest(".view-question-btn").dataset.questionId,s=document.querySelector('input[name="language"]:checked'),a=s?s.value:null;if(!a){Swal.fire({title:((i=window.translations)==null?void 0:i.attention)||"Diqqat!",text:((t=window.translations)==null?void 0:t.selectLanguage)||"Iltimos tilni tanlang",icon:"warning",confirmButtonText:"OK",customClass:{confirmButton:"btn btn-primary"},buttonsStyling:!1});return}this.loadQuestionDetails(o,a)}}),document.addEventListener("click",e=>{if(e.target.closest(".delete-btn")){const i=e.target.closest(".delete-btn").dataset.questionId;this.confirmDelete(i)}})}filterQuestions(){var o,s,a;const e=(o=document.querySelector('input[name="language"]:checked'))==null?void 0:o.value,i=(s=document.getElementById("searchInput"))==null?void 0:s.value,t=new URL(window.location.origin+window.location.pathname);e&&t.searchParams.set("language_id",e),i&&t.searchParams.set("search",i);const n=document.getElementById("questionsContainer");n&&(n.innerHTML=`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">${((a=window.translations)==null?void 0:a.loading)||"Yuklanmoqda..."}</span>
                    </div>
                </div>
            `),fetch(t.toString(),{headers:{"X-Requested-With":"XMLHttpRequest"}}).then(r=>r.text()).then(r=>{const l=document.createElement("div");l.innerHTML=r;const d=l.querySelector("#questionsContainer");if(d&&n){n.innerHTML=d.innerHTML;const c=document.querySelector('input[name="language"]:checked');c&&n.querySelectorAll(".view-question-btn").forEach(m=>{m.setAttribute("data-language-id",c.value)}),this.applyCSSStyles()}history.pushState(null,"",t.toString())}).catch(r=>{var l;console.error("Error:",r),n&&(n.innerHTML=`
                        <div class="alert alert-danger text-center">
                            ${((l=window.translations)==null?void 0:l.errorLoading)||"Savollarni yuklashda xatolik"}
                        </div>
                    `)})}applyCSSStyles(){document.querySelectorAll(".question-card").forEach(t=>{t.style.transition="all 0.3s ease",t.addEventListener("mouseenter",()=>{t.style.transform="translateY(-3px)",t.style.boxShadow="0 8px 25px rgba(0, 0, 0, 0.15)"}),t.addEventListener("mouseleave",()=>{t.style.transform="translateY(0)",t.style.boxShadow="0 2px 10px rgba(0, 0, 0, 0.1)"})}),document.querySelectorAll(".btn-outline-info, .btn-outline-warning, .btn-outline-danger").forEach(t=>{t.addEventListener("mouseenter",()=>{t.style.transform="translateY(-1px)"}),t.addEventListener("mouseleave",()=>{t.style.transform="translateY(0)"})})}loadQuestionDetails(e,i){var o;const t=new bootstrap.Modal(document.getElementById("questionModal")),n=document.getElementById("questionModalBody");n&&(n.innerHTML=`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">${((o=window.translations)==null?void 0:o.loading)||"Yuklanmoqda..."}</span>
                    </div>
                </div>
            `),t.show(),fetch(`/admin/questions/${e}/show?language_id=${i}`).then(s=>s.json()).then(s=>{s.success?this.displayQuestionDetails(s.data,i):n&&(n.innerHTML=`<div class="alert alert-danger">${s.message}</div>`)}).catch(s=>{var a;console.error("Error:",s),n&&(n.innerHTML=`
                        <div class="alert alert-danger">
                            ${((a=window.translations)==null?void 0:a.errorLoadingDetails)||"Savol tafsilotlarini yuklashda xatolik"}
                        </div>
                    `)})}displayQuestionDetails(e,i){var o,s,a;const t=document.getElementById("questionModalBody");if(!t)return;let n="";e.translation&&e.translation.image&&(n+=`
                <div class="text-center mb-4">
                    <img src="/storage/${e.translation.image}" 
                         class="img-fluid modal-question-image" 
                         alt="Question Image">
                </div>
            `),n+=`
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <h6 class="text-primary mb-0 me-2">
                        <i class="icofont icofont-question-circle me-2"></i>
                        ${((o=window.translations)==null?void 0:o.question)||"Savol"}
                    </h6>
                    <span class="badge bg-primary">${e.translation?e.translation.language.name:"N/A"}</span>
                </div>
                <div class="p-3 bg-light rounded">
                    <p class="mb-0 fs-6 text-dark fw-semibold">${e.translation?e.translation.text:((s=window.translations)==null?void 0:s.noTranslation)||"Tarjima mavjud emas"}</p>
                </div>
            </div>
        `,n+=`
            <div>
                <h6 class="text-success mb-3">
                    <i class="icofont icofont-ui-check me-2"></i>
                    ${((a=window.translations)==null?void 0:a.answerOptions)||"Javob variantlari"}
                </h6>
        `,e.answers.forEach((r,l)=>{var u;const d=r.translation?r.translation.text:((u=window.translations)==null?void 0:u.noTranslation)||"Tarjima mavjud emas",c=r.is_correct;n+=`
                <div class="answer-item mb-3 p-3 rounded ${c?"bg-success bg-opacity-25 border-success":"bg-light border-secondary"}" style="border: 1px solid;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge ${c?"bg-success":"bg-secondary"}">
                            ${String.fromCharCode(65+l)}
                        </span>
                        ${c?'<i class="icofont icofont-check-circled text-success fs-5"></i>':""}
                    </div>
                    <p class="mb-0 text-dark">${d}</p>
                </div>
            `}),n+="</div>",t.innerHTML=n}confirmDelete(e){var i,t,n,o;Swal.fire({title:((i=window.translations)==null?void 0:i.deleteQuestion)||"Savolni o'chirish",text:((t=window.translations)==null?void 0:t.deleteConfirmText)||"Bu savolni o'chirishga ishonchingiz komilmi? Bu amal qaytarib bo'lmaydi!",icon:"warning",showCancelButton:!0,confirmButtonColor:"#dc3545",cancelButtonColor:"#6c757d",confirmButtonText:((n=window.translations)==null?void 0:n.confirmDelete)||"Ha, o'chirish!",cancelButtonText:((o=window.translations)==null?void 0:o.cancel)||"Bekor qilish",reverseButtons:!0,focusCancel:!0,customClass:{actions:"d-flex gap-3 justify-content-center",confirmButton:"btn btn-danger me-2",cancelButton:"btn btn-secondary"},buttonsStyling:!1}).then(s=>{s.isConfirmed&&this.deleteQuestion(e)})}deleteQuestion(e){var t,n,o;Swal.fire({title:((t=window.translations)==null?void 0:t.deleting)||"O'chirilmoqda...",text:((n=window.translations)==null?void 0:n.pleaseWait)||"Iltimos, kuting",allowOutsideClick:!1,allowEscapeKey:!1,showConfirmButton:!1,didOpen:()=>{Swal.showLoading()}});const i=(o=document.querySelector('meta[name="csrf-token"]'))==null?void 0:o.getAttribute("content");fetch(`/admin/questions/${e}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":i,"Content-Type":"application/json"}}).then(s=>s.json()).then(s=>{var a,r,l,d;s.success?Swal.fire({title:((a=window.translations)==null?void 0:a.deleted)||"O'chirildi!",text:((r=window.translations)==null?void 0:r.deleteSuccess)||"Savol muvaffaqiyatli o'chirildi.",icon:"success",confirmButtonText:"OK",customClass:{confirmButton:"btn btn-success"},buttonsStyling:!1}).then(()=>{var h;const c=(h=document.querySelector(`[data-question-id="${e}"]`))==null?void 0:h.closest(".col-lg-3");c&&c.remove();const u=document.getElementById("questionsContainer"),m=u==null?void 0:u.querySelectorAll(".question-card");(!m||m.length===0)&&location.reload()}):Swal.fire({title:((l=window.translations)==null?void 0:l.error)||"Xatolik!",text:s.message||((d=window.translations)==null?void 0:d.deleteError)||"Savolni o'chirishda xatolik yuz berdi.",icon:"error",confirmButtonText:"OK",customClass:{confirmButton:"btn btn-primary"},buttonsStyling:!1})}).catch(s=>{var a,r;console.error("Error:",s),Swal.fire({title:((a=window.translations)==null?void 0:a.error)||"Xatolik!",text:((r=window.translations)==null?void 0:r.serverError)||"Server bilan bog'lanishda xatolik yuz berdi.",icon:"error",confirmButtonText:"OK",customClass:{confirmButton:"btn btn-primary"},buttonsStyling:!1})})}}document.addEventListener("DOMContentLoaded",()=>{new f});
