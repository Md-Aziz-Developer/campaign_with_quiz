<form id="add-question-form" class="row g-3">
    @csrf
    <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
    <input type="hidden" id="editing-question-id" name="editing_question_id" value="">
    <div class="col-12">
        <label for="question_text" class="form-label">Question text *</label>
        <input type="text" class="form-control" name="question_text" id="question_text" required>
    </div>
    <div class="col-md-4">
        <label for="question_type" class="form-label">Type</label>
        <select class="form-select" name="type" id="question_type">
            <option value="mcq_single">MCQ Single</option>
            <option value="mcq_multi">MCQ Multi</option>
            <option value="text">Text</option>
            <option value="number">Number</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">&nbsp;</label>
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="is_mandatory" id="is_mandatory" value="1">
            <label class="form-check-label" for="is_mandatory">Mandatory</label>
        </div>
    </div>
    <div class="col-12" id="type-specific-fields">
        {{-- Filled by JS --}}
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary" id="add-question-btn">Add Question</button>
        <button type="button" class="btn btn-outline-secondary ms-2" id="cancel-edit-btn" style="display:none">Cancel</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('add-question-form');
    var typeSelect = document.getElementById('question_type');
    var container = document.getElementById('type-specific-fields');
    var campaignId = '{{ $campaign->id }}';
    var storeUrl = '{{ route("admin.campaigns.questions.store", $campaign) }}';
    var editingInput = document.getElementById('editing-question-id');
    var addBtn = document.getElementById('add-question-btn');
    var cancelEditBtn = document.getElementById('cancel-edit-btn');

    function setEditMode(questionId) {
        editingInput.value = questionId || '';
        addBtn.textContent = questionId ? 'Update Question' : 'Add Question';
        cancelEditBtn.style.display = questionId ? 'inline-block' : 'none';
    }

    function populateForm(q) {
        document.getElementById('question_text').value = q.question_text || '';
        typeSelect.value = q.type || 'mcq_single';
        document.getElementById('is_mandatory').checked = !!q.is_mandatory;
        renderTypeFields();
        if (q.type === 'mcq_single' || q.type === 'mcq_multi') {
            var list = document.getElementById('options-list');
            if (list && q.options && q.options.length) {
                list.innerHTML = '';
                q.options.forEach(function(opt) {
                    addOptionRow();
                    var rows = list.querySelectorAll('.input-group');
                    var last = rows[rows.length - 1];
                    if (last) {
                        last.querySelector('.option-text').value = opt.option_text || '';
                        last.querySelector('.option-score').value = opt.score != null ? opt.score : '';
                        last.querySelector('.is-correct').checked = !!opt.is_correct;
                    }
                });
            }
        } else if (q.type === 'text') {
            var kwList = document.getElementById('keyword-rules-list');
            if (kwList) {
                kwList.innerHTML = '';
                var textKw = q.textKeyword || q.text_keyword;
                var rules = (textKw && (textKw.rules || textKw.keyword_rules)) ? (textKw.rules || textKw.keyword_rules) : [];
                if (rules.length === 0) {
                    addKeywordRow();
                } else {
                    rules.forEach(function(r) {
                        addKeywordRow();
                        var rows = kwList.querySelectorAll('.input-group');
                        var last = rows[rows.length - 1];
                        if (last) {
                            last.querySelector('input[name="kw_keyword[]"]').value = (r.keyword !== undefined ? r.keyword : r.keyword_text) || '';
                            last.querySelector('input[name="kw_score[]"]').value = (r.score != null ? r.score : '');
                        }
                    });
                }
            }
        } else if (q.type === 'number') {
            var nrList = document.getElementById('number-rules-list');
            if (nrList) {
                nrList.innerHTML = '';
                var rules = q.numberRules || q.number_rules || [];
                function fmtNum(val) {
                    if (val === null || val === undefined || val === '') return '';
                    var n = parseFloat(val);
                    if (isNaN(n)) return '';
                    return Number(n.toFixed(2)).toString();
                }
                if (rules.length === 0) {
                    addNumberRuleRow();
                } else {
                    rules.forEach(function(r) {
                        addNumberRuleRow();
                        var rows = nrList.querySelectorAll('.row.g-1');
                        var last = rows[rows.length - 1];
                        if (last) {
                            last.querySelector('.exact').value = fmtNum(r.exact_value);
                            last.querySelector('.min').value = fmtNum(r.min_value);
                            last.querySelector('.max').value = fmtNum(r.max_value);
                            var scoreInput = last.querySelector('input[name="nr_score[]"]');
                            if (scoreInput) scoreInput.value = fmtNum(r.score);
                        }
                    });
                }
            }
        }
    }

    var questionsListEl = document.getElementById('questions-list');
    if (questionsListEl) {
        questionsListEl.addEventListener('click', function(e) {
            var btn = e.target.closest('.edit-question-btn');
            if (!btn) return;
            var questionId = btn.getAttribute('data-id');
            if (!questionId) return;
            setEditMode(questionId);
            fetch(storeUrl + '/' + questionId, { headers: { 'Accept': 'application/json' } })
                .then(function(r) { return r.json(); })
                .then(function(question) {
                    populateForm(question);
                    form.scrollIntoView({ behavior: 'smooth' });
                })
                .catch(function(err) {
                    console.error(err);
                    alert('Could not load question');
                    setEditMode('');
                });
        });
    }

    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function() {
            setEditMode('');
            document.getElementById('question_text').value = '';
            renderTypeFields();
        });
    }

    function renderTypeFields() {
        var type = typeSelect.value;
        container.innerHTML = '';
        if (type === 'mcq_single' || type === 'mcq_multi') {
            container.innerHTML = '<label class="form-label">Options</label><div id="options-list"></div><button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="add-option">+ Add option</button>';
            addOptionRow();
            addOptionRow();
        } else if (type === 'text') {
            container.innerHTML = '<label class="form-label">Keyword rules (keyword → score)</label><div id="keyword-rules-list"></div><button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="add-keyword">+ Add keyword</button>';
            addKeywordRow();
            addKeywordRow();
        } else if (type === 'number') {
            container.innerHTML = '<label class="form-label">Number rules (exact or range + score)</label><p class="text-muted small mb-1">Per rule: use <strong>Exact</strong> only, or <strong>Min + Max</strong> only—not both.</p><div id="number-rules-list"></div><button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="add-number-rule">+ Add rule</button>';
            addNumberRuleRow();
        }
        bindTypeButtons();
    }

    function addOptionRow() {
        var list = document.getElementById('options-list');
        if (!list) return;
        var div = document.createElement('div');
        div.className = 'input-group mb-1';
        div.innerHTML = '<input type="text" class="form-control option-text" placeholder="Option text" name="opt_text[]">' +
            '<input type="number" step="0.01" class="form-control option-score" placeholder="Score" name="opt_score[]" style="max-width:80px">' +
            '<div class="form-check form-check-inline align-self-center ms-2"><input class="form-check-input is-correct" type="checkbox" name="opt_correct[]" value="1"><label class="form-check-label small">Correct</label></div>';
        list.appendChild(div);
    }

    function addKeywordRow() {
        var list = document.getElementById('keyword-rules-list');
        if (!list) return;
        var div = document.createElement('div');
        div.className = 'input-group mb-1';
        div.innerHTML = '<input type="text" class="form-control" placeholder="Keyword" name="kw_keyword[]">' +
            '<input type="number" step="0.01" class="form-control" placeholder="Score" name="kw_score[]" style="max-width:80px">';
        list.appendChild(div);
    }

    function addNumberRuleRow() {
        var list = document.getElementById('number-rules-list');
        if (!list) return;
        var div = document.createElement('div');
        div.className = 'row g-1 mb-1 number-rule-row';
        div.innerHTML = '<div class="col"><input type="number" step="any" class="form-control form-control-sm exact" placeholder="Exact" name="nr_exact[]"></div>' +
            '<div class="col"><input type="number" step="any" class="form-control form-control-sm min" placeholder="Min" name="nr_min[]"></div>' +
            '<div class="col"><input type="number" step="any" class="form-control form-control-sm max" placeholder="Max" name="nr_max[]"></div>' +
            '<div class="col"><input type="number" step="0.01" class="form-control form-control-sm" placeholder="Score" name="nr_score[]"></div>';
        list.appendChild(div);
    }

    form.addEventListener('input', function(e) {
        var row = e.target.closest('.number-rule-row');
        if (!row) return;
        if (e.target.classList.contains('exact') && e.target.value.trim() !== '') {
            row.querySelector('.min').value = '';
            row.querySelector('.max').value = '';
        } else if ((e.target.classList.contains('min') || e.target.classList.contains('max')) && e.target.value.trim() !== '') {
            row.querySelector('.exact').value = '';
        }
    });

    function bindTypeButtons() {
        var addOpt = document.getElementById('add-option');
        if (addOpt) addOpt.onclick = addOptionRow;
        var addKw = document.getElementById('add-keyword');
        if (addKw) addKw.onclick = addKeywordRow;
        var addNr = document.getElementById('add-number-rule');
        if (addNr) addNr.onclick = addNumberRuleRow;
    }

    typeSelect.addEventListener('change', renderTypeFields);
    renderTypeFields();

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var type = typeSelect.value;
        var data = new FormData();
        data.append('_token', form.querySelector('input[name=_token]').value);
        data.append('question_text', document.getElementById('question_text').value);
        data.append('type', type);
        data.append('is_mandatory', document.getElementById('is_mandatory').checked ? '1' : '0');

        if (type === 'mcq_single' || type === 'mcq_multi') {
            var texts = form.querySelectorAll('.option-text');
            var scores = form.querySelectorAll('.option-score');
            var corrects = form.querySelectorAll('.is-correct');
            var options = [];
            for (var i = 0; i < texts.length; i++) {
                if (texts[i].value.trim()) {
                    options.push({
                        option_text: texts[i].value.trim(),
                        score: parseFloat(scores[i]?.value) || 0,
                        is_correct: corrects[i]?.checked || false
                    });
                }
            }
            data.append('options', JSON.stringify(options));
        } else if (type === 'text') {
            var kwK = form.querySelectorAll('input[name="kw_keyword[]"]');
            var kwS = form.querySelectorAll('input[name="kw_score[]"]');
            var rules = [];
            for (var j = 0; j < kwK.length; j++) {
                if (kwK[j].value.trim()) {
                    rules.push({ keyword: kwK[j].value.trim(), score: parseFloat(kwS[j]?.value) || 0 });
                }
            }
            data.append('keyword_rules', JSON.stringify(rules));
        } else if (type === 'number') {
            var nrExact = form.querySelectorAll('input[name="nr_exact[]"]');
            var nrMin = form.querySelectorAll('input[name="nr_min[]"]');
            var nrMax = form.querySelectorAll('input[name="nr_max[]"]');
            var nrScore = form.querySelectorAll('input[name="nr_score[]"]');
            var nrules = [];
            for (var k = 0; k < nrScore.length; k++) {
                var exact = (nrExact[k]?.value ?? '').toString().trim();
                var min = (nrMin[k]?.value ?? '').toString().trim();
                var max = (nrMax[k]?.value ?? '').toString().trim();
                var sc = (nrScore[k]?.value ?? '').toString().trim();
                if (sc === '') continue;
                if (exact !== '') {
                    nrules.push({ exact_value: exact, min_value: null, max_value: null, score: parseFloat(sc) });
                } else if (min !== '' && max !== '') {
                    nrules.push({ exact_value: null, min_value: min, max_value: max, score: parseFloat(sc) });
                }
            }
            data.append('number_rules', JSON.stringify(nrules));
        }

        var btn = document.getElementById('add-question-btn');
        btn.disabled = true;
        var editingId = editingInput.value.trim();
        var url = storeUrl;
        var method = 'POST';
        if (editingId) {
            url = storeUrl + '/' + editingId;
            data.append('_method', 'PUT');
        }
        var csrfToken = (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || form.querySelector('input[name=_token]').value;
        var headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };
        fetch(url, {
            method: method,
            body: data,
            headers: headers
        }).then(function(r) { return r.json(); })
        .then(function(res) {
            btn.disabled = false;
            if (res.success && res.html) {
                var list = document.getElementById('questions-list');
                if (list) {
                    if (editingId) {
                        var row = list.querySelector('li[data-question-id="' + editingId + '"]');
                        if (row) {
                            var wrap = document.createElement('div');
                            wrap.innerHTML = res.html;
                            row.replaceWith(wrap.firstElementChild);
                        }
                    } else {
                        list.insertAdjacentHTML('beforeend', res.html);
                    }
                    var emptyMsg = document.getElementById('questions-empty-msg');
                    if (emptyMsg) emptyMsg.style.display = 'none';
                }
                setEditMode('');
                document.getElementById('question_text').value = '';
                if (document.getElementById('options-list')) document.getElementById('options-list').innerHTML = '';
                renderTypeFields();
                var successEl = document.getElementById('question-form-success');
                var successText = document.getElementById('question-form-success-text');
                if (successEl && successText) {
                    successText.textContent = editingId ? 'Question updated.' : 'Question added.';
                    successEl.classList.remove('d-none');
                    successEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    setTimeout(function() { successEl.classList.add('d-none'); }, 4000);
                }
            } else if (res.errors) {
                alert(Object.values(res.errors).flat().join('\n'));
            } else {
                alert(res.message || 'Error adding question');
            }
        }).catch(function(err) {
            btn.disabled = false;
            console.error(err);
            alert('Request failed');
        });
    });
});
</script>
