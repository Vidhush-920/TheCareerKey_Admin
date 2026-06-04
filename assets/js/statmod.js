async function filterLatest(data) {
    const latest_attempts = {};
    data.forEach(record => {
        const nic = record.nic ?? 'Unknown NIC';
        const log_no = record.log_no ?? 0;
        if (!(nic in latest_attempts) || latest_attempts[nic].log_no < log_no) {
            latest_attempts[nic] = record;
        }
    });
    return latest_attempts;
}

async function filterRecords(records, filter_type) {
    const today = new Date();
    
    if (filter_type === 'this-year') {
        const current_year = today.getFullYear();
        return records.filter(record => record.created_at.getFullYear() === current_year);
    } else if (filter_type === 'this-month') {
        const current_month = today.getMonth();
        const current_year = today.getFullYear();
        return records.filter(record => record.created_at.getMonth() === current_month && record.created_at.getFullYear() === current_year);
    } else if (filter_type === 'this-week') {
        const firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
        firstDay.setHours(0, 0, 0, 0);
        const lastDay = new Date(firstDay);
        lastDay.setDate(lastDay.getDate() + 6);
        lastDay.setHours(23, 59, 59, 999);
        return records.filter(record => record.created_at >= firstDay && record.created_at <= lastDay);
    } else if (filter_type === 'today') {
        const current_date = today.getDate();
        const current_month = today.getMonth();
        const current_year = today.getFullYear();
        return records.filter(record => record.created_at.getDate() === current_date && record.created_at.getMonth() === current_month && record.created_at.getFullYear() === current_year);
    }
    return records;
}

async function filterRecordsByDateRange(records, start_date, end_date) {
    let start = null;
    let end = null;
    
    if (start_date) {
        const [y, m, d] = start_date.split('-');
        start = new Date(y, m - 1, d);
        start.setHours(0, 0, 0, 0);
    }
    
    if (end_date) {
        const [y, m, d] = end_date.split('-');
        end = new Date(y, m - 1, d);
        end.setHours(23, 59, 59, 999);
    }
    
    const filtered = records.filter(record => {
        const rec_date = record.created_at;
        const afterStart = start ? rec_date >= start : true;
        const beforeEnd = end ? rec_date <= end : true;
        return afterStart && beforeEnd;
    });
    return filtered;
}

function top3Categories(record) {
    const riasec = {"R": record.r ?? 0, "I": record.i ?? 0, "A": record.a ?? 0, "S": record.s ?? 0, "E": record.e ?? 0, "C": record.c ?? 0};
    const sorted = Object.entries(riasec).sort((a, b) => b[1] - a[1]);
    const top3 = sorted.slice(0, 3).map(entry => entry[0]).join('');
    return top3;
}

function nthScoreCategory(record, n) {
    const riasec = {"R": record.r ?? 0, "I": record.i ?? 0, "A": record.a ?? 0, "S": record.s ?? 0, "E": record.e ?? 0, "C": record.c ?? 0};
    const sorted = Object.entries(riasec).sort((a, b) => b[1] - a[1]);
    if (n-1 < sorted.length) {
        return {category: sorted[n-1][0], score: sorted[n-1][1]};
    }
    return null;
}

function isAdjacent(record) {
    const adjacents = {
        "R": ["I", "C"],
        "I": ["R", "A"],
        "A": ["I", "S"],
        "S": ["A", "E"],
        "E": ["S", "C"],
        "C": ["E", "R"]
    };
    const maxCat = nthScoreCategory(record, 1)?.category;
    const max2_cat = nthScoreCategory(record, 2)?.category;
    if (maxCat && max2_cat) {
        return adjacents[maxCat].includes(max2_cat);
    }
}

function isOpposite(record) {
    const opposites = [ ["R", "S"], ["I", "E"], ["A", "C"] ];
    const maxCat = nthScoreCategory(record, 1)?.category;
    const max2_cat = nthScoreCategory(record, 2)?.category;
    if (maxCat && max2_cat) {
        return opposites.some(pair => (pair[0] === maxCat && pair[1] === max2_cat) || (pair[0] === max2_cat && pair[1] === maxCat));
    }
}

function consistencyScore(record) {
    const maxCat = nthScoreCategory(record, 1);
    const max2_cat = nthScoreCategory(record, 2);
    if (maxCat && max2_cat) {
        const adj_score = maxCat.score - max2_cat.score;
        const penalty_factor = isAdjacent(record) ? 1 : (isOpposite(record) ? 0 : 0.5);
        return adj_score === 0 ? 100 : Math.max(0, (1 - (adj_score / maxCat.score)) * 100 * penalty_factor);
    }
    return 0;
}

async function calculateRecStat(record) {
    record.top3 = top3Categories(record);

    const maxCat = nthScoreCategory(record, 1);
    record.maxCat = maxCat;
    const minCat = nthScoreCategory(record, 6);
    record.minCat = minCat;
    record.score_range = maxCat.score - (minCat?.score ?? 0);
    const max2_cat = nthScoreCategory(record, 2)?.category;
    const max2_score = nthScoreCategory(record, 2)?.score ?? 0;
    record.adj_range = maxCat.score - max2_score;
    record.are_adjacent = isAdjacent(record);
    record.is_opposite = isOpposite(record);
    record.consistency_score = consistencyScore(record);
}

async function calculateRecListStat(latest_attempts) {
    for (const [nic, record] of Object.entries(latest_attempts)) {
        await calculateRecStat(record);
    }
}

async function maxCategoryData(latest_attempts) {
    let category_counts = {"R": 0, "I": 0, "A": 0, "S": 0, "E": 0, "C": 0};
    for (const record of Object.values(latest_attempts)) {
        if (record.maxCat && record.maxCat.category) {
            category_counts[record.maxCat.category]++;
        }
    }
    return category_counts;
}

async function minCategoryData(latest_attempts) {
    let category_counts = {"R": 0, "I": 0, "A": 0, "S": 0, "E": 0, "C": 0};
    for (const record of Object.values(latest_attempts)) {
        if (record.minCat && record.minCat.category) {
            category_counts[record.minCat.category]++;
        }
    }
    return category_counts;
}

async function top3CategoriesCount(latest_attempts) {
    let top3_counts = {};
    for (const record of Object.values(latest_attempts)) {
        if (record.top3) {
            if (!(record.top3 in top3_counts)) {
                top3_counts[record.top3] = 0;
            }
            top3_counts[record.top3]++;
        }
    }
    top3_counts = Object.fromEntries(Object.entries(top3_counts).sort((a, b) => b[1] - a[1]).slice(0, 10)); // sort by count and get top 10
    return top3_counts;
}