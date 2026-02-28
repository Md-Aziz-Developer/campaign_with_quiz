<form id="add-question-form" class="row g-3">
    @csrf
    <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
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
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('add-question-form');
    var typeSelect = document.getElementById('question_type');
    var container = document.getElementById('type-specific-fields');
    var campaignId = '{{ $campaign->id }}';
    var storeUrl = '{{ route("admin.campaigns.questions.store", $campaign) }}';

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
            container.innerHTML = '<label class="form-label">Number rules (exact or range + score)</label><div id="number-rules-list"></div><button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="add-number-rule">+ Add rule</button>';
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
        div.className = 'row g-1 mb-1';
        div.innerHTML = '<div class="col"><input type="number" step="any" class="form-control form-control-sm exact" placeholder="Exact" name="nr_exact[]"></div>' +
            '<div class="col"><input type="number" step="any" class="form-control form-control-sm min" placeholder="Min" name="nr_min[]"></div>' +
            '<div class="col"><input type="number" step="any" class="form-control form-control-sm max" placeholder="Max" name="nr_max[]"></div>' +
            '<div class="col"><input type="number" step="0.01" class="form-control form-control-sm" placeholder="Score" name="nr_score[]"></div>';
        list.appendChild(div);
    }

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
                var exact = nrExact[k]?.value;
                var min = nrMin[k]?.value;
                var max = nrMax[k]?.value;
                var sc = nrScore[k]?.value;
                if (sc !== '' && (exact !== '' || (min !== '' && max !== ''))) {
                    nrules.push({
                        exact_value: exact !== '' ? exact : null,
                        min_value: min !== '' ? min : null,
                        max_value: max !== '' ? max : null,
                        score: parseFloat(sc)
                    });
                }
            }
            data.append('number_rules', JSON.stringify(nrules));
        }

        var btn = document.getElementById('add-question-btn');
        btn.disabled = true;
        fetch(storeUrl, {
            method: 'POST',
            body: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).then(function(r) { return r.json(); })
        .then(function(res) {
            btn.disabled = false;
            if (res.success && res.html) {
                document.getElementById('questions-list').insertAdjacentHTML('beforeend', res.html);
                document.getElementById('question_text').value = '';
                if (document.getElementById('options-list')) document.getElementById('options-list').innerHTML = '';
                renderTypeFields();
            } else if (res.errors) {
                alert(Object.values(res.errors).flat().join('\n'));
            } else {
                alert(res.message || 'Error adding question');
            }
        }).catch(function() {
            btn.disabled = false;
            alert('Request failed');
        });
    });
});
</script>
