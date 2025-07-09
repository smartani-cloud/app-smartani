<<<<<<< HEAD
$(document).ready(function()
{
    console.log('Load data siswa');
    var mytable = $('#dataTable').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "sDom": 'lfrtip',
        buttons: [
            'excelHtml5',
        ],
    });
    $.ajax({
        url : '/kependidikan/kbm/siswa/onLoad',
        type : "GET",
        dataType : "json",
        success:function(data)
        {
            $.each(data,function(key,value){
                $('tbody[id="table_siswa"]').append('<tr id='+key+'></tr>');
                $('tr[id='+key+']').append(
                        '<td id="hide" style="display:none" >'+value.reg_number+'</td>'+
                        '<td id="hide" style="display:none" > $siswa->unit->name </td>'+
                        '<td id="hide" style="display:none" > $siswa->join_date </td>'+
                        '<td id="hide" style="display:none" > $siswa->semester_id?$siswa->semester->semester_id:'-' </td>'+
                        '<td id="hide" style="display:none" >$siswa->level_id?$siswa->level->level:'-' </td>'+
                        '<td>$siswa->student_nis</td>'+
                        '<td> $siswa->student_nisn </td>'+
                        '<td> $siswa->student_name </td>'+
                        '<td id="hide" style="display:none" > $siswa->student_nickname </td>'+
                        '<td id="hide" style="display:none" > $siswa->birth_place </td>'+
                        '<td>$siswa->birth_date </td>'+
                        '<td> $siswa->gender_id </td>'+
                        '<td id="hide" style="display:none" > $siswa->religion_id</td>'+
                        '<td id="hide" style="display:none" > $siswa->child_of</td>'+
                        '<td id="hide" style="display:none" > $siswa->family_status </td>'+
                        '<td id="hide" style="display:none" > $siswa->address </td>'+
                        '<td id="hide" style="display:none" > $siswa->address_number </td>'+
                        '<td id="hide" style="display:none" > $siswa->rt </td>'+
                        '<td id="hide" style="display:none" > $siswa->rw </td>'+
                        '<td id="hide" style="display:none" >region_id</td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_name</td>'+
                        '<td id="hide" style="display:none" >{$siswa->orangtua->father_nik </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_phone </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_email </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_job </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_position </td>'+
                        '<td id="hide" style="display:none" >$siswa->orangtua->father_phone_office </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_job_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_salary </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_name </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_nik </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_phone </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_email </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_job </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_position </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_phone_office </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_job_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_salary </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->employee_id </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->parent_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->parent_phone_number </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_name </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_nik </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_phone_number </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_email </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_job</td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_position </td>'+
                        '<td id="hide" style="display:none" >$siswa->orangtua->guardian_phone_office</td>'+
                        '<td id="hide" style="display:none" >$siswa->orangtua->guardian_job_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_salary </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->origin_school ,  $siswa->origin_school_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->sibling_name?$siswa->sibling_name: </td>'+
                        '<td id="hide" style="display:none" > ($siswa->sibling_level_id)?$siswa->levelsaudara->level: </td>'+
                        '<td id="hide" style="display:none" > $siswa->info_from </td>'+
                        '<td id="hide" style="display:none" > $siswa->info_name </td>'+
                        '<td id="hide" style="display:none" > $siswa->position </td>'+
                        '<td id="hide" style="display:none" > $siswa->class_id </td>'+
                        '<td>'+
                            '<a href="siswa/lihat/" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;'+
                            '<a href="siswa/ubah/" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;'+
                            '<a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#HapusModal"><i class="fas fa-trash"></i></a>'+
                        '</td>'
                );
                console.log('Load data siswa');
            });
        },
        error: function () {
          alert('error');
        }
    });
=======
$(document).ready(function()
{
    console.log('Load data siswa');
    var mytable = $('#dataTable').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "sDom": 'lfrtip',
        buttons: [
            'excelHtml5',
        ],
    });
    $.ajax({
        url : '/kependidikan/kbm/siswa/onLoad',
        type : "GET",
        dataType : "json",
        success:function(data)
        {
            $.each(data,function(key,value){
                $('tbody[id="table_siswa"]').append('<tr id='+key+'></tr>');
                $('tr[id='+key+']').append(
                        '<td id="hide" style="display:none" >'+value.reg_number+'</td>'+
                        '<td id="hide" style="display:none" > $siswa->unit->name </td>'+
                        '<td id="hide" style="display:none" > $siswa->join_date </td>'+
                        '<td id="hide" style="display:none" > $siswa->semester_id?$siswa->semester->semester_id:'-' </td>'+
                        '<td id="hide" style="display:none" >$siswa->level_id?$siswa->level->level:'-' </td>'+
                        '<td>$siswa->student_nis</td>'+
                        '<td> $siswa->student_nisn </td>'+
                        '<td> $siswa->student_name </td>'+
                        '<td id="hide" style="display:none" > $siswa->student_nickname </td>'+
                        '<td id="hide" style="display:none" > $siswa->birth_place </td>'+
                        '<td>$siswa->birth_date </td>'+
                        '<td> $siswa->gender_id </td>'+
                        '<td id="hide" style="display:none" > $siswa->religion_id</td>'+
                        '<td id="hide" style="display:none" > $siswa->child_of</td>'+
                        '<td id="hide" style="display:none" > $siswa->family_status </td>'+
                        '<td id="hide" style="display:none" > $siswa->address </td>'+
                        '<td id="hide" style="display:none" > $siswa->address_number </td>'+
                        '<td id="hide" style="display:none" > $siswa->rt </td>'+
                        '<td id="hide" style="display:none" > $siswa->rw </td>'+
                        '<td id="hide" style="display:none" >region_id</td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_name</td>'+
                        '<td id="hide" style="display:none" >{$siswa->orangtua->father_nik </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_phone </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_email </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_job </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_position </td>'+
                        '<td id="hide" style="display:none" >$siswa->orangtua->father_phone_office </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_job_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->father_salary </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_name </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_nik </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_phone </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_email </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_job </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_position </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_phone_office </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_job_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->mother_salary </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->employee_id </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->parent_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->parent_phone_number </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_name </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_nik </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_phone_number </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_email </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_job</td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_position </td>'+
                        '<td id="hide" style="display:none" >$siswa->orangtua->guardian_phone_office</td>'+
                        '<td id="hide" style="display:none" >$siswa->orangtua->guardian_job_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_salary </td>'+
                        '<td id="hide" style="display:none" > $siswa->orangtua->guardian_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->origin_school ,  $siswa->origin_school_address </td>'+
                        '<td id="hide" style="display:none" > $siswa->sibling_name?$siswa->sibling_name: </td>'+
                        '<td id="hide" style="display:none" > ($siswa->sibling_level_id)?$siswa->levelsaudara->level: </td>'+
                        '<td id="hide" style="display:none" > $siswa->info_from </td>'+
                        '<td id="hide" style="display:none" > $siswa->info_name </td>'+
                        '<td id="hide" style="display:none" > $siswa->position </td>'+
                        '<td id="hide" style="display:none" > $siswa->class_id </td>'+
                        '<td>'+
                            '<a href="siswa/lihat/" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;'+
                            '<a href="siswa/ubah/" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;'+
                            '<a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#HapusModal"><i class="fas fa-trash"></i></a>'+
                        '</td>'
                );
                console.log('Load data siswa');
            });
        },
        error: function () {
          alert('error');
        }
    });
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
});