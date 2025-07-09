<<<<<<< HEAD
<form action="{{route('sertifiklas.simpan')}}" method="POST">
    @csrf
    <input type="hidden" name="siswa_id" value="{{$siswa->id}}">
    <table class="table align-items-center table-flush">
        <thead class="bg-brand-green text-white">
            <tr>
                <th width="60%">Kompetensi</th>
                <th class="text-center">Predikat</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-dark text-white">
                <td colspan="2"><strong>1. ISLAMI</strong></td>
            </tr>
            <tr>
                <td>1.1. Iman</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[1][1]" value="5" id="511" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[0]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="511">☆</label>
                        <input type="radio" name="rating[1][1]" value="4" id="411" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[0]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="411">☆</label>
                        <input type="radio" name="rating[1][1]" value="3" id="311" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[0]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="311">☆</label>
                        <input type="radio" name="rating[1][1]" value="2" id="211" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[0]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="211">☆</label>
                        <input type="radio" name="rating[1][1]" value="1" id="111" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[0]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="111">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>1.2. Ibadah</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[1][2]" value="5" id="512" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[1]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="512">☆</label>
                        <input type="radio" name="rating[1][2]" value="4" id="412" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[1]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="412">☆</label>
                        <input type="radio" name="rating[1][2]" value="3" id="312" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[1]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="312">☆</label>
                        <input type="radio" name="rating[1][2]" value="2" id="212" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[1]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="212">☆</label>
                        <input type="radio" name="rating[1][2]" value="1" id="112" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[1]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="112">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>1.3. Akhlak</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[1][3]" value="5" id="513" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[2]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="513">☆</label>
                        <input type="radio" name="rating[1][3]" value="4" id="413" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[2]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="413">☆</label>
                        <input type="radio" name="rating[1][3]" value="3" id="313" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[2]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="313">☆</label>
                        <input type="radio" name="rating[1][3]" value="2" id="213" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[2]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="213">☆</label>
                        <input type="radio" name="rating[1][3]" value="1" id="113" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[2]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="113">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>1.4. Quran</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[1][4]" value="5" id="514" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[3]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="514">☆</label>
                        <input type="radio" name="rating[1][4]" value="4" id="414" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[3]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="414">☆</label>
                        <input type="radio" name="rating[1][4]" value="3" id="314" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[3]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="314">☆</label>
                        <input type="radio" name="rating[1][4]" value="2" id="214" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[3]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="214">☆</label>
                        <input type="radio" name="rating[1][4]" value="1" id="114" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[3]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="114">☆</label>
                    </div>
                </td>
            </tr>
            <tr class="bg-dark text-white">
                <td colspan="2"><strong>2. KARAKTER SUKSES</strong></td>
            </tr>
            <tr>
                <td>2.1. Disiplin</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[2][1]" value="5" id="521" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[4]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="521">☆</label>
                        <input type="radio" name="rating[2][1]" value="4" id="421" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[4]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="421">☆</label>
                        <input type="radio" name="rating[2][1]" value="3" id="321" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[4]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="321">☆</label>
                        <input type="radio" name="rating[2][1]" value="2" id="221" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[4]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="221">☆</label>
                        <input type="radio" name="rating[2][1]" value="1" id="121" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[4]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="121">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2.2. Jujur</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[2][2]" value="5" id="522" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[5]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="522">☆</label>
                        <input type="radio" name="rating[2][2]" value="4" id="422" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[5]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="422">☆</label>
                        <input type="radio" name="rating[2][2]" value="3" id="322" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[5]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="322">☆</label>
                        <input type="radio" name="rating[2][2]" value="2" id="222" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[5]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="222">☆</label>
                        <input type="radio" name="rating[2][2]" value="1" id="122" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[5]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="122">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2.3. Tanggung Jawab</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[2][3]" value="5" id="523" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[6]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="523">☆</label>
                        <input type="radio" name="rating[2][3]" value="4" id="423" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[6]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="423">☆</label>
                        <input type="radio" name="rating[2][3]" value="3" id="323" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[6]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="323">☆</label>
                        <input type="radio" name="rating[2][3]" value="2" id="223" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[6]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="223">☆</label>
                        <input type="radio" name="rating[2][3]" value="1" id="123" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[6]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="123">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2.4. Peduli</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[2][4]" value="5" id="524" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[7]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="524">☆</label>
                        <input type="radio" name="rating[2][4]" value="4" id="424" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[7]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="424">☆</label>
                        <input type="radio" name="rating[2][4]" value="3" id="324" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[7]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="324">☆</label>
                        <input type="radio" name="rating[2][4]" value="2" id="224" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[7]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="224">☆</label>
                        <input type="radio" name="rating[2][4]" value="1" id="124" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[7]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="124">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2.5. Bermental Juara</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[2][5]" value="5" id="525" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[8]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="525">☆</label>
                        <input type="radio" name="rating[2][5]" value="4" id="425" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[8]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="425">☆</label>
                        <input type="radio" name="rating[2][5]" value="3" id="325" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[8]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="325">☆</label>
                        <input type="radio" name="rating[2][5]" value="2" id="225" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[8]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="225">☆</label>
                        <input type="radio" name="rating[2][5]" value="1" id="125" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[8]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="125">☆</label>
                    </div>
                </td>
            </tr>
            <tr class="bg-dark text-white">
                <td colspan="2"><strong>3. LITERASI ERA 4.0</strong></td>
            </tr>
            <tr>
                <td>3.1. Literasi Digital</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[3][1]" value="5" id="531" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[9]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="531">☆</label>
                        <input type="radio" name="rating[3][1]" value="4" id="431" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[9]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="431">☆</label>
                        <input type="radio" name="rating[3][1]" value="3" id="331" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[9]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="331">☆</label>
                        <input type="radio" name="rating[3][1]" value="2" id="231" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[9]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="231">☆</label>
                        <input type="radio" name="rating[3][1]" value="1" id="131" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[9]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="131">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>3.2. Literasi Bahasa Inggris</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[3][2]" value="5" id="532" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[10]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="532">☆</label>
                        <input type="radio" name="rating[3][2]" value="4" id="432" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[10]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="432">☆</label>
                        <input type="radio" name="rating[3][2]" value="3" id="332" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[10]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="332">☆</label>
                        <input type="radio" name="rating[3][2]" value="2" id="232" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[10]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="232">☆</label>
                        <input type="radio" name="rating[3][2]" value="1" id="132" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[10]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="132">☆</label>
                    </div>
                </td>
            </tr>

            <tr class="bg-dark text-white">
                <td colspan="2"><strong>4. SKILL ABAD 21</strong></td>
            </tr>
            <tr>
                <td>4.1. Kritis</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[4][1]" value="5" id="541" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[11]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="541">☆</label>
                        <input type="radio" name="rating[4][1]" value="4" id="441" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[11]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="441">☆</label>
                        <input type="radio" name="rating[4][1]" value="3" id="341" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[11]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="341">☆</label>
                        <input type="radio" name="rating[4][1]" value="2" id="241" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[11]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="241">☆</label>
                        <input type="radio" name="rating[4][1]" value="1" id="141" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[11]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="141">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>4.2. Kreatif</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[4][2]" value="5" id="542" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[12]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="542">☆</label>
                        <input type="radio" name="rating[4][2]" value="4" id="442" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[12]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="442">☆</label>
                        <input type="radio" name="rating[4][2]" value="3" id="342" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[12]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="342">☆</label>
                        <input type="radio" name="rating[4][2]" value="2" id="242" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[12]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="242">☆</label>
                        <input type="radio" name="rating[4][2]" value="1" id="142" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[12]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="142">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>4.3. Komunikatif</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[4][3]" value="5" id="543" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[13]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="543">☆</label>
                        <input type="radio" name="rating[4][3]" value="4" id="443" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[13]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="443">☆</label>
                        <input type="radio" name="rating[4][3]" value="3" id="343" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[13]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="343">☆</label>
                        <input type="radio" name="rating[4][3]" value="2" id="243" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[13]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="243">☆</label>
                        <input type="radio" name="rating[4][3]" value="1" id="143" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[13]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="143">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>4.4. Kolaboratif</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[4][4]" value="5" id="544" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[14]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="544">☆</label>
                        <input type="radio" name="rating[4][4]" value="4" id="444" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[14]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="444">☆</label>
                        <input type="radio" name="rating[4][4]" value="3" id="344" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[14]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="344">☆</label>
                        <input type="radio" name="rating[4][4]" value="2" id="244" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[14]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="244">☆</label>
                        <input type="radio" name="rating[4][4]" value="1" id="144" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[14]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="144">☆</label>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
    </div>
=======
<form action="{{route('sertifiklas.simpan')}}" method="POST">
    @csrf
    <input type="hidden" name="siswa_id" value="{{$siswa->id}}">
    <table class="table align-items-center table-flush">
        <thead class="bg-brand-green text-white">
            <tr>
                <th width="60%">Kompetensi</th>
                <th class="text-center">Predikat</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-dark text-white">
                <td colspan="2"><strong>1. ISLAMI</strong></td>
            </tr>
            <tr>
                <td>1.1. Iman</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[1][1]" value="5" id="511" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[0]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="511">☆</label>
                        <input type="radio" name="rating[1][1]" value="4" id="411" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[0]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="411">☆</label>
                        <input type="radio" name="rating[1][1]" value="3" id="311" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[0]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="311">☆</label>
                        <input type="radio" name="rating[1][1]" value="2" id="211" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[0]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="211">☆</label>
                        <input type="radio" name="rating[1][1]" value="1" id="111" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[0]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="111">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>1.2. Ibadah</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[1][2]" value="5" id="512" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[1]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="512">☆</label>
                        <input type="radio" name="rating[1][2]" value="4" id="412" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[1]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="412">☆</label>
                        <input type="radio" name="rating[1][2]" value="3" id="312" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[1]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="312">☆</label>
                        <input type="radio" name="rating[1][2]" value="2" id="212" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[1]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="212">☆</label>
                        <input type="radio" name="rating[1][2]" value="1" id="112" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[1]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="112">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>1.3. Akhlak</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[1][3]" value="5" id="513" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[2]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="513">☆</label>
                        <input type="radio" name="rating[1][3]" value="4" id="413" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[2]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="413">☆</label>
                        <input type="radio" name="rating[1][3]" value="3" id="313" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[2]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="313">☆</label>
                        <input type="radio" name="rating[1][3]" value="2" id="213" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[2]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="213">☆</label>
                        <input type="radio" name="rating[1][3]" value="1" id="113" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[2]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="113">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>1.4. Quran</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[1][4]" value="5" id="514" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[3]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="514">☆</label>
                        <input type="radio" name="rating[1][4]" value="4" id="414" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[3]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="414">☆</label>
                        <input type="radio" name="rating[1][4]" value="3" id="314" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[3]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="314">☆</label>
                        <input type="radio" name="rating[1][4]" value="2" id="214" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[3]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="214">☆</label>
                        <input type="radio" name="rating[1][4]" value="1" id="114" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[3]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="114">☆</label>
                    </div>
                </td>
            </tr>
            <tr class="bg-dark text-white">
                <td colspan="2"><strong>2. KARAKTER SUKSES</strong></td>
            </tr>
            <tr>
                <td>2.1. Disiplin</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[2][1]" value="5" id="521" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[4]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="521">☆</label>
                        <input type="radio" name="rating[2][1]" value="4" id="421" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[4]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="421">☆</label>
                        <input type="radio" name="rating[2][1]" value="3" id="321" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[4]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="321">☆</label>
                        <input type="radio" name="rating[2][1]" value="2" id="221" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[4]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="221">☆</label>
                        <input type="radio" name="rating[2][1]" value="1" id="121" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[4]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="121">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2.2. Jujur</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[2][2]" value="5" id="522" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[5]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="522">☆</label>
                        <input type="radio" name="rating[2][2]" value="4" id="422" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[5]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="422">☆</label>
                        <input type="radio" name="rating[2][2]" value="3" id="322" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[5]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="322">☆</label>
                        <input type="radio" name="rating[2][2]" value="2" id="222" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[5]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="222">☆</label>
                        <input type="radio" name="rating[2][2]" value="1" id="122" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[5]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="122">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2.3. Tanggung Jawab</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[2][3]" value="5" id="523" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[6]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="523">☆</label>
                        <input type="radio" name="rating[2][3]" value="4" id="423" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[6]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="423">☆</label>
                        <input type="radio" name="rating[2][3]" value="3" id="323" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[6]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="323">☆</label>
                        <input type="radio" name="rating[2][3]" value="2" id="223" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[6]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="223">☆</label>
                        <input type="radio" name="rating[2][3]" value="1" id="123" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[6]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="123">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2.4. Peduli</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[2][4]" value="5" id="524" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[7]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="524">☆</label>
                        <input type="radio" name="rating[2][4]" value="4" id="424" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[7]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="424">☆</label>
                        <input type="radio" name="rating[2][4]" value="3" id="324" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[7]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="324">☆</label>
                        <input type="radio" name="rating[2][4]" value="2" id="224" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[7]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="224">☆</label>
                        <input type="radio" name="rating[2][4]" value="1" id="124" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[7]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="124">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2.5. Bermental Juara</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[2][5]" value="5" id="525" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[8]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="525">☆</label>
                        <input type="radio" name="rating[2][5]" value="4" id="425" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[8]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="425">☆</label>
                        <input type="radio" name="rating[2][5]" value="3" id="325" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[8]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="325">☆</label>
                        <input type="radio" name="rating[2][5]" value="2" id="225" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[8]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="225">☆</label>
                        <input type="radio" name="rating[2][5]" value="1" id="125" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[8]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="125">☆</label>
                    </div>
                </td>
            </tr>
            <tr class="bg-dark text-white">
                <td colspan="2"><strong>3. LITERASI ERA 4.0</strong></td>
            </tr>
            <tr>
                <td>3.1. Literasi Digital</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[3][1]" value="5" id="531" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[9]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="531">☆</label>
                        <input type="radio" name="rating[3][1]" value="4" id="431" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[9]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="431">☆</label>
                        <input type="radio" name="rating[3][1]" value="3" id="331" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[9]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="331">☆</label>
                        <input type="radio" name="rating[3][1]" value="2" id="231" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[9]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="231">☆</label>
                        <input type="radio" name="rating[3][1]" value="1" id="131" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[9]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="131">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>3.2. Literasi Bahasa Inggris</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[3][2]" value="5" id="532" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[10]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="532">☆</label>
                        <input type="radio" name="rating[3][2]" value="4" id="432" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[10]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="432">☆</label>
                        <input type="radio" name="rating[3][2]" value="3" id="332" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[10]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="332">☆</label>
                        <input type="radio" name="rating[3][2]" value="2" id="232" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[10]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="232">☆</label>
                        <input type="radio" name="rating[3][2]" value="1" id="132" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[10]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="132">☆</label>
                    </div>
                </td>
            </tr>

            <tr class="bg-dark text-white">
                <td colspan="2"><strong>4. SKILL ABAD 21</strong></td>
            </tr>
            <tr>
                <td>4.1. Kritis</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[4][1]" value="5" id="541" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[11]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="541">☆</label>
                        <input type="radio" name="rating[4][1]" value="4" id="441" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[11]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="441">☆</label>
                        <input type="radio" name="rating[4][1]" value="3" id="341" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[11]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="341">☆</label>
                        <input type="radio" name="rating[4][1]" value="2" id="241" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[11]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="241">☆</label>
                        <input type="radio" name="rating[4][1]" value="1" id="141" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[11]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="141">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>4.2. Kreatif</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[4][2]" value="5" id="542" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[12]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="542">☆</label>
                        <input type="radio" name="rating[4][2]" value="4" id="442" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[12]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="442">☆</label>
                        <input type="radio" name="rating[4][2]" value="3" id="342" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[12]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="342">☆</label>
                        <input type="radio" name="rating[4][2]" value="2" id="242" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[12]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="242">☆</label>
                        <input type="radio" name="rating[4][2]" value="1" id="142" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[12]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="142">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>4.3. Komunikatif</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[4][3]" value="5" id="543" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[13]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="543">☆</label>
                        <input type="radio" name="rating[4][3]" value="4" id="443" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[13]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="443">☆</label>
                        <input type="radio" name="rating[4][3]" value="3" id="343" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[13]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="343">☆</label>
                        <input type="radio" name="rating[4][3]" value="2" id="243" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[13]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="243">☆</label>
                        <input type="radio" name="rating[4][3]" value="1" id="143" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[13]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="143">☆</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>4.4. Kolaboratif</td>
                <td>
                    <div class="rating">
                        <input type="radio" name="rating[4][4]" value="5" id="544" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[14]->predicate == 5) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="544">☆</label>
                        <input type="radio" name="rating[4][4]" value="4" id="444" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[14]->predicate == 4) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="444">☆</label>
                        <input type="radio" name="rating[4][4]" value="3" id="344" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[14]->predicate == 3) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="344">☆</label>
                        <input type="radio" name="rating[4][4]" value="2" id="244" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[14]->predicate == 2) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="244">☆</label>
                        <input type="radio" name="rating[4][4]" value="1" id="144" <?php if ($scoreiklas != FALSE) {
                                                                                        if ($scoreiklas[14]->predicate == 1) {
                                                                                            echo "checked";
                                                                                        }
                                                                                    } ?>><label for="144">☆</label>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
    </div>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</form>