<form action="{{ route($route.'.children.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">ID</div>
    <div class="col-12">
      <h5>{{ $data->id }}</h5>
    </div>
  </div>
  @if($data->father_name)
  <div class="row mb-2">
    <div class="col-12 mb-1">Nama Ayah</div>
    <div class="col-12">
      <h5>{{ $data->father_name }}</h5>
    </div>
  </div>
  @endif
  @if($data->mother_name)
  <div class="row mb-2">
    <div class="col-12 mb-1">Nama Ibu</div>
    <div class="col-12">
      <h5>{{ $data->mother_name }}</h5>
    </div>
  </div>
  @endif
  @if($data->guardian_name)
  <div class="row mb-2">
    <div class="col-12 mb-1">Nama Wali</div>
    <div class="col-12">
      <h5>{{ $data->guardian_name }}</h5>
    </div>
  </div>
  @endif
  @if($data->username)
  <div class="row mb-2">
    <div class="col-12 mb-1">Username</div>
    <div class="col-12">
      <h5>{{ $data->username }}</h5>
    </div>
  </div>
  @endif
  @if($data->childrensCount > 0)
  @if($data->siswas()->count() > 0 && $data->calonSiswa()->count() > 0)
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="childrenOpt" class="form-control-label">Anak</label>
          </div>
          <div class="col-12">
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="childrenOpt1" name="childrenOpt" class="custom-control-input" value="student" required="required" checked>
              <label class="custom-control-label" for="childrenOpt1">Siswa</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="childrenOpt2" name="childrenOpt" class="custom-control-input" value="candidate" required="required">
              <label class="custom-control-label" for="childrenOpt2">Calon Siswa</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  @if($data->siswas()->count() > 0)
  <div id="studentRow">
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <div class="row">
            <div class="col-12">
              <label for="optStudent" class="form-control-label">Siswa</label>
            </div>
            <div class="col-12">
              <select aria-label="Student" name="student" id="optStudent" title="Student" class="select2 form-control">
                @foreach($data->siswas()->select('id','student_name')->orderBy('birth_date','desc')->get() as $student)
                <option value="{{ $student->id }}">{{ $student->student_name.($student->latestLevel ? ' - '.$student->latestLevel : null) }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  @if($data->calonSiswa()->count() > 0)
  <div id="candidateRow" {!! $data->siswas()->count() > 0 ? 'style="display: none;"' : null !!}>
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <div class="row">
            <div class="col-12">
              <label for="optCandidate" class="form-control-label">Calon Siswa</label>
            </div>
            <div class="col-12">
              <select aria-label="Candidate" name="candidate" id="optCandidate" title="Candidate" class="select2 form-control">
                @foreach($data->calonSiswa()->select('id','student_name','unit_id','level_id')->orderBy('birth_date','desc')->get() as $candidate)
                <option value="{{ $candidate->id }}">{{ $candidate->student_name.($candidate->level ? ' - '.$candidate->level->level : null) }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="optParent" class="form-control-label">Orang Tua Tujuan</label>
          </div>
          <div class="col-12">
            <select aria-label="Parent" name="parent" id="optParent" title="Parent" class="select2 form-control">
              @foreach($parents as $parent)
              @php
              $name = ($parent->father_name ? $parent->father_name.($parent->mother_name ? '/' : null) : null).($parent->mother_name ? $parent->mother_name.($parent->guardian_name ? '/' : null) : null).($parent->guardian_name ? $parent->guardian_name : null);
              @endphp
              <option value="{{ $parent->id }}">{{ $parent->id.($name ? ' - '.$name : null).($parent->username ? ' - '.$parent->username : null) }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <div class="row mt-3">
    <div class="col-6 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
    <div class="col-6 text-right">
      @if($data->childrensCount > 0 && $parents && count($parents) > 0)
      <input id="save-data" type="submit" class="btn btn-brand-purple-dark" value="Pindah">
      @else
      <button type="button" class="btn btn-sm btn-secondary" disabled="disabled">Pindah</button>
      @endif
    </div>
  </div>
</form>

@if($data->siswas()->count() > 0 && $data->calonSiswa()->count() > 0)
<script>
    $(document).ready(function () {
      $('input[name="childrenOpt"]').on('change',function(){
        var childrenOpt = $(this).val();
        if(childrenOpt == 'student'){
          $('#studentRow').show();
          $('#candidateRow').hide();
        }else{
          $('#studentRow').hide();
          $('#candidateRow').show();
        }
      });
    });
</script>
@endif

@include('template.footjs.global.select2-default')