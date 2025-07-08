        <div class="row mb-0">
          <div class="col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Tanggal</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->dateId ? $data->dateId : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nama Proposal</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->title ? $data->title : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Unit</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->unit_id ? $data->unit->name : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Jabatan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->position_id ? $data->jabatan->name : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Status</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->ppa ? 'Diajukan' : 'Menunggu' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Tahap</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  @if(!$data->date)
                  <span class="badge badge-secondary">Draft</span>
                  @else
                  @if(!$data->ppa)
                  <span class="badge badge-info">Diajukan ke PA</span>
                  @else
                  <span class="badge badge-success">Proses PPA</span>
                  @endif
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
        @if($data->anggaran)
        <div class="row mb-0">
          <div class="col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Tujuan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->anggaran->accJabatan->name.' - '.$data->anggaran->name }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        
        @if(in_array(Auth::user()->role->name,['ketuayys','direktur','fam','faspv']) && $data->ppa)
        <div class="row mb-0">
          <div class="col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nomor PPA</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  <a href="{{ route('ppa.show',['jenis' => $data->ppa->ppa->jenisAnggaranAnggaran->jenis->link, 'tahun' => $data->ppa->ppa->academic_year_id ? $data->ppa->ppa->tahunPelajaran->academicYearLink : $data->ppa->ppa->year, 'anggaran' => $data->ppa->ppa->jenisAnggaranAnggaran->anggaran->link, 'nomor' => $data->ppa->ppa->firstNumber]) }}" target="_blank" class="text-decoration-none text-info">{{ $data->ppa->ppa->number }}</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        
        @if($data->desc)
        <div class="row mb-0">
          <div class="col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Deskripsi</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->desc }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>    

      <div class="row mt-3">
        <div class="col-12 text-right">
          <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Kembali</button>
        </div>
      </div>
