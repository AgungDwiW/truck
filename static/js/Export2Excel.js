function generateArray(table) {
    var out = [];
    var rows = table.querySelectorAll('tr');
    var ranges = [];
    next_rowSpan = [];
    length_before= rows[0].querySelectorAll('th, td').length

    for (var R = 0; R < rows.length; ++R) {
    // for (var R = 0; R < 2; ++R) {
        var outRow = [];
        var row = rows[R];
        th = row.querySelectorAll('th');
        td = row.querySelectorAll('td');
        var columns = [...th, ...td];
        cur_length = columns.length;
        cur_rowSpan = []
        if (columns.length ==0 )
            continue
        index_col = 0;
        index_colSpan = 0;
        length = cur_length;
        if (length<length_before)
            length = length_before;

        for (let index = 0; index < length; index++) {
            if(next_rowSpan[index]){
                outRow.push(null)
            }
                
            else{
                // console.log([index_col, columns, td, th])
                if (!columns[index_col])
                    continue
                var cell = columns[index_col];
                var colspan = cell.getAttribute('colspan');
                var rowspan = cell.getAttribute('rowspan');
                // cell_JQ = $(cell)
                var cellValue = cell.innerText;
                // console.log(cellValue);
                if(cellValue !== "" && cellValue == +cellValue) cellValue = +cellValue;

                //Skip ranges
                ranges.forEach(function(range) {
                    if(R >= range.s.r && R <= range.e.r && outRow.length >= range.s.c && outRow.length <= range.e.c) {
                        for(var i = 0; i <= range.e.c - range.s.c; ++i) outRow.push(null);
                    }
                });
                if (rowspan){
                    cur_rowSpan[index_col+index_colSpan] = true
                    if (colspan) {
                        for (var k = 0; k < colspan-1 ; k++){
                            cur_rowSpan[index_col+k+index_colSpan] = true
                        } 
                    }   
                }
                //Handle Value
                outRow.push(cellValue !== "" ? cellValue : null);

                if (colspan) {
                    for (var k = 1; k < colspan ; k++){
                        outRow.push(null);
                        cur_length+=1;
                        index_colSpan+=1;
                    } 
                }
                index_col+=1;

            }
        }
        // console.log([R, length_before, next_rowSpan, cur_rowSpan])
        next_rowSpan = cur_rowSpan;
        length_before= cur_length;
        out.push(outRow);
        // console.log(["OUUTTTT",outRow]);
    }
        
        
    return [out, ranges];
};



function datenum(v, date1904) {
	if(date1904) v+=1462;
	var epoch = Date.parse(v);
	return (epoch - new Date(Date.UTC(1899, 11, 30))) / (24 * 60 * 60 * 1000);
}
 
function sheet_from_array_of_arrays(data, opts) {
	var ws = {};
	var range = {s: {c:10000000, r:10000000}, e: {c:0, r:0 }};
	for(var R = 0; R != data.length; ++R) {
		for(var C = 0; C != data[R].length; ++C) {
			if(range.s.r > R) range.s.r = R;
			if(range.s.c > C) range.s.c = C;
			if(range.e.r < R) range.e.r = R;
			if(range.e.c < C) range.e.c = C;
			var cell = {v: data[R][C] };
			if(cell.v == null) continue;
			var cell_ref = XLSX.utils.encode_cell({c:C,r:R});
			
			if(typeof cell.v === 'number') cell.t = 'n';
			else if(typeof cell.v === 'boolean') cell.t = 'b';
			else if(cell.v instanceof Date) {
				cell.t = 'n'; cell.z = XLSX.SSF._table[14];
				cell.v = datenum(cell.v);
			}
			else cell.t = 's';
			
			ws[cell_ref] = cell;
		}
	}
	if(range.s.c < 10000000) ws['!ref'] = XLSX.utils.encode_range(range);
	return ws;
}
 
function Workbook() {
	if(!(this instanceof Workbook)) return new Workbook();
	this.SheetNames = [];
	this.Sheets = {};
}
 
function s2ab(s) {
	var buf = new ArrayBuffer(s.length);
	var view = new Uint8Array(buf);
	for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
	return buf;
}

function export_table_to_excel(id, name) {
    var theTable = document.getElementById(id);
    var oo = generateArray(theTable);
    var ranges = oo[1];

    /* original data */
    var data = oo[0]; 
    var ws_name = "data";
    // console.log(data); 

    var wb = new Workbook(), ws = sheet_from_array_of_arrays(data);
    
    /* add ranges to worksheet */
    ws['!merges'] = ranges;

    /* add worksheet to workbook */
    wb.SheetNames.push(ws_name);
    wb.Sheets[ws_name] = ws;

    var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:false, type: 'binary'});

    saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), name)
}


function downloadCSVFile(csv_data, filename = "table.csv") {
 
    // Create CSV file object and feed our
    // csv_data into it
    CSVFile = new Blob([csv_data], { type: "text/csv" });
 
    // Create to temporary link to initiate
    // download process
    var temp_link = document.createElement('a');
 
    // Download csv file
    temp_link.download = filename;
    var url = window.URL.createObjectURL(CSVFile);
    temp_link.href = url;
 
    // This link should not be displayed
    temp_link.style.display = "none";
    document.body.appendChild(temp_link);
 
    // Automatically click the link to trigger download
    temp_link.click();
    document.body.removeChild(temp_link);
    
}

function exportTableToExcel(id, name) {
    const table = document.getElementById(id);
    const rows = table.rows;
    const data = [];
    const merges = [];

    // Track cell positions
    const cellMatrix = [];

    for (let r = 0; r < rows.length; r++) {
    const row = rows[r];
    const cells = row.cells;
    const rowData = [];
    let colIndex = 0;

    // Ensure cellMatrix[r] exists
    if (!cellMatrix[r]) cellMatrix[r] = [];

    for (let c = 0; c < cells.length; c++) {
        const cell = cells[c];

        // Skip filled positions (due to rowspan/colspan)
        while (cellMatrix[r][colIndex]) colIndex++;

        const cellValue = cell.innerText;
        const rowspan = cell.rowSpan || 1;
        const colspan = cell.colSpan || 1;

        rowData[colIndex] = cellValue;

        // Mark merged cells in matrix
        for (let i = 0; i < rowspan; i++) {
        for (let j = 0; j < colspan; j++) {
            if (!cellMatrix[r + i]) cellMatrix[r + i] = [];
            cellMatrix[r + i][colIndex + j] = true;
        }
        }

        // Record merge if needed
        if (rowspan > 1 || colspan > 1) {
        merges.push({
            s: { r: r, c: colIndex },
            e: { r: r + rowspan - 1, c: colIndex + colspan - 1 }
        });
        }

        colIndex += colspan;
    }

    data.push(rowData);
    }


    // Create worksheet and apply merges
    const worksheet = XLSX.utils.aoa_to_sheet(data);
    worksheet["!merges"] = merges;
    merges.forEach(({ s }) => {
    const cellRef = XLSX.utils.encode_cell(s);
    if (worksheet[cellRef]) {
        worksheet[cellRef].s = {
        alignment: {
            horizontal: "center",
            vertical: "center"
        }
        };
    }
    });

    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
    XLSX.writeFile(workbook, name);
}
