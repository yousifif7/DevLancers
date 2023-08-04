<form class="mx-1 mx-md-4" method="post" action="/users/{{ Auth::user()->id }}">
    @csrf
    @method('PUT')
    <div class="d-flex flex-row align-items-center mb-4">
        <div class="form-outline flex-fill mb-0">
            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
            <label class="form-label" for="form3Example1c">Your Name</label>
            <input type="text" id="form3Example1c" class="form-control" name="name"
                value="{{ Auth::user()->name }}" />
            @error('name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- <div class="d-flex flex-row align-items-center mb-4">
      <div class="form-outline flex-fill mb-0">
        <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
        <label class="form-label" for="form3Example4c">Password</label>
        <input type="password" id="form3Example4c" class="form-control" name="password"/>
        @error('password')
          <p class="text-danger">{{$message}}</p>
        @enderror
      </div>
    </div>

    <div class="d-flex flex-row align-items-center mb-4">
      <div class="form-outline flex-fill mb-0">
        <i class="fas fa-key fa-lg me-3 fa-fw"></i>
        <label class="form-label" for="form3Example4cd">Repeat your password</label>
        <input type="password" id="form3Example4cd" class="form-control" name="password_confirmation"/>
        @error('password_confirmation')
          <p class="text-danger">{{$message}}</p>
        @enderror
      </div>
    </div> --}}


    <div class="d-flex flex-row align-items-center mb-4">
        <div class="form-outline flex-fill mb-0">
            <i class="fa-solid fa-venus-mars"></i>
            <label class="form-label" for="form3Example4cd">Your Gender</label>
                @if (Auth::user()->gender == 'Male')
                    <input type="radio" class="form-check-input " name="gender" value="Male" id="malegender"
                        checked>
                    <label>Male</label>
                    <input type="radio" class="form-check-input " name="gender" value="Female" id="femalegender">
                    <label>Female</label>
                    <input type="radio" class="form-check-input " name="gender" value="Other" id="othergender">
                    <label>Other</label>
                @elseif (Auth::user()->gender == 'Female')
                    <input type="radio" class="form-check-input " name="gender" value="Male" id="malegender">
                    <label>Male</label>
                    <input type="radio" class="form-check-input " name="gender" value="Female" id="femalegender"
                        checked>
                    <label>Female</label>
                    <input type="radio" class="form-check-input " name="gender" value="Other" id="othergender">
                    <label>Other</label>
                @else
                    <input type="radio" class="form-check-input " name="gender" value="Male" id="malegender">
                    <label>Male</label>
                    <input type="radio" class="form-check-input " name="gender" value="Female" id="femalegender">
                    <label>Female</label>
                    <input type="radio" class="form-check-input " name="gender" value="Other" id="othergender"
                        checked>
                    <label>Other</label>
                @endif
            @error('gender')
                <p class="text-danger">Please choose your gender</p>
            @enderror
        </div>
    </div>

    <div class="d-flex flex-row align-items-center mb-4">
        <div class="form-outline flex-fill mb-0">
            <i class="fa-solid fa-book"></i>
            <label class="form-label" for="form6Example7">Bio</label>
            <textarea class="form-control" id="form6Example7" rows="4" name="bio">{{ Auth::user()->bio }}</textarea>
        </div>
    </div>

    <div class="d-flex flex-row align-items-center mb-4">
        <div class="form-outline flex-fill mb-0">
            <i class="fa-sharp fa-solid fa-location-dot"></i>
            <label class="form-label" for="form6Example8">address</label>
            <select class="form-select" id="country" name="address">
                <option value="{{Auth::user()->address}}" selected>{{Auth::user()->address}}</option>
                <option value="Afghanistan">Afghanistan</option>
                <option value="AAland Islands">Aland Islands</option>
                <option value="Albania">Albania</option>
                <option value="Algeria">Algeria</option>
                <option value="American Samoa">American Samoa</option>
                <option value="Andorra">Andorra</option>
                <option value="Angola">Angola</option>
                <option value="Anguilla">Anguilla</option>
                <option value="Antarctica">Antarctica</option>
                <option value="Argentina">Argentina</option>
                <option value="Armenia">Armenia</option>
                <option value="Aruba">Aruba</option>
                <option value="Australia">Australia</option>
                <option value="Austria">Austria</option>
                <option value="Azerbaijan">Azerbaijan</option>
                <option value="Bahamas">Bahamas</option>
                <option value="Bahrain">Bahrain</option>
                <option value="Bangladesh">Bangladesh</option>
                <option value="Barbados">Barbados</option>
                <option value="Belarus">Belarus</option>
                <option value="Belgium">Belgium</option>
                <option value="Bhutan">Bhutan</option>
                <option value="Bolivia">Bolivia</option>
                <option value="Botswana">Botswana</option>
                <option value="Brazil">Brazil</option>
                <option value="Bulgaria">Bulgaria</option>
                <option value="Burkina Faso">Burkina Faso</option>
                <option value="Cambodia">Cambodia</option>
                <option value="Cameroon">Cameroon</option>
                <option value="Canada">Canada</option>
                <option value="Chad">Chad</option>
                <option value="Chile">Chile</option>
                <option value="China">China</option>
                <option value="Colombia">Colombia</option>
                <option value="Comoros">Comoros</option>
                <option value="Congo">Congo</option>
                <option value="Democratic Republic of the Congo">Congo, Democratic Republic of the Congo</option>
                <option value="Costa Rica">Costa Rica</option>
                <option value="Cote D'Ivoire">Cote D'Ivoire</option>
                <option value="Croatia">Croatia</option>
                <option value="Cuba">Cuba</option>
                <option value="Curacao">Curacao</option>
                <option value="Cyprus">Cyprus</option>
                <option value="Czech Republic">Czech Republic</option>
                <option value="Denmark">Denmark</option>
                <option value="Djibouti">Djibouti</option>
                <option value="Dominica">Dominica</option>
                <option value="Dominican Republic">Dominican Republic</option>
                <option value="Ecuador">Ecuador</option>
                <option value="Egypt">Egypt</option>
                <option value="El Salvador">El Salvador</option>
                <option value="Equatorial Guinea">Equatorial Guinea</option>
                <option value="Eritrea">Eritrea</option>
                <option value="Estonia">Estonia</option>
                <option value="Ethiopia">Ethiopia</option>
                <option value="Fiji">Fiji</option>
                <option value="Finland">Finland</option>
                <option value="France">France</option>
                <option value="Gabon">Gabon</option>
                <option value="Gambia">Gambia</option>
                <option value="Georgia">Georgia</option>
                <option value="Germany">Germany</option>
                <option value="Ghana">Ghana</option>
                <option value="Greece">Greece</option>
                <option value="Greenland">Greenland</option>
                <option value="Guam">Guam</option>
                <option value="Guatemala">Guatemala</option>
                <option value="Guinea">Guinea</option>
                <option value="Guinea-Bissau">Guinea-Bissau</option>
                <option value="Guyana">Guyana</option>
                <option value="Haiti">Haiti</option>
                <option value="Honduras">Honduras</option>
                <option value="Hong Kong">Hong Kong</option>
                <option value="Hungary">Hungary</option>
                <option value="Iceland">Iceland</option>
                <option value="India">India</option>
                <option value="Indonesia">Indonesia</option>
                <option value="Iran">Iran, Islamic Republic of</option>
                <option value="Iraq">Iraq</option>
                <option value="Ireland">Ireland</option>
                <option value="Israel">Israel</option>
                <option value="Italy">Italy</option>
                <option value="Jamaica">Jamaica</option>
                <option value="Japan">Japan</option>
                <option value="Jordan">Jordan</option>
                <option value="Kazakhstan">Kazakhstan</option>
                <option value="Kenya">Kenya</option>
                <option value="Korea">Korea, Republic of</option>
                <option value="Kosovo">Kosovo</option>
                <option value="Kuwait">Kuwait</option>
                <option value="Kyrgyzstan">Kyrgyzstan</option>
                <option value="Latvia">Latvia</option>
                <option value="Lebanon">Lebanon</option>
                <option value="Liberia">Liberia</option>
                <option value="Libya">Libya</option>
                <option value="Lithuania">Lithuania</option>
                <option value="Luxembourg">Luxembourg</option>
                <option value="Macedonia">Macedonia</option>
                <option value="Madagascar">Madagascar</option>
                <option value="Malawi">Malawi</option>
                <option value="Malaysia">Malaysia</option>
                <option value="Mali">Mali</option>
                <option value="Malta">Malta</option>
                <option value="Mexico">Mexico</option>
                <option value="Mongolia">Mongolia</option>
                <option value="Morocco">Morocco</option>
                <option value="Mozambique">Mozambique</option>
                <option value="Myanmar">Myanmar</option>
                <option value="Namibia">Namibia</option>
                <option value="Nepal">Nepal</option>
                <option value="Netherlands">Netherlands</option>
                <option value="New Zealand">New Zealand</option>
                <option value="Nicaragua">Nicaragua</option>
                <option value="Niger">Niger</option>
                <option value="Nigeria">Nigeria</option>
                <option value="Norway">Norway</option>
                <option value="Oman">Oman</option>
                <option value="Pakistan">Pakistan</option>
                <option value="Palau">Palau</option>
                <option value="Palestine">Palestine</option>
                <option value="Panama">Panama</option>
                <option value="Paraguay">Paraguay</option>
                <option value="Peru">Peru</option>
                <option value="Philippines">Philippines</option>
                <option value="Poland">Poland</option>
                <option value="Portugal">Portugal</option>
                <option value="Qatar">Qatar</option>
                <option value="Romania">Romania</option>
                <option value="Russian Federation">Russian Federation</option>
                <option value="Saudi Arabia">Saudi Arabia</option>
                <option value="Senegal">Senegal</option>
                <option value="Serbia">Serbia</option>
                <option value="Singapore">Singapore</option>
                <option value="Slovakia">Slovakia</option>
                <option value="Slovenia">Slovenia</option>
                <option value="Somalia">Somalia</option>
                <option value="South Africa">South Africa</option>
                <option value="Spain">Spain</option>
                <option value="Sri Lanka">Sri Lanka</option>
                <option value="SD">Sudan</option>
                <option value="Swaziland">Swaziland</option>
                <option value="Sweden">Sweden</option>
                <option value="Switzerland">Switzerland</option>
                <option value="Syria">Syria</option>
                <option value="Tajikistan">Tajikistan</option>
                <option value="Tanzania">Tanzania, United Republic of</option>
                <option value="Thailand">Thailand</option>
                <option value="Togo">Togo</option>
                <option value="Tunisia">Tunisia</option>
                <option value="Turkey">Turkey</option>
                <option value="Uganda">Uganda</option>
                <option value="Ukraine">Ukraine</option>
                <option value="UAE">United Arab Emirates</option>
                <option value="United Kingdom">United Kingdom</option>
                <option value="USA">United States</option>
                <option value="Uruguay">Uruguay</option>
                <option value="Uzbekistan">Uzbekistan</option>
                <option value="Venezuela">Venezuela</option>
                <option value="VietNam">Viet Nam</option>
                <option value="Yemen">Yemen</option>
                <option value="Zambia">Zambia</option>
                <option value="Zimbabwe">Zimbabwe</option>
            </select>
        </div>
    </div>

    <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
        <button class="gigbtn btn-lg w-100">Confirm</button>
    </div>

</form>
