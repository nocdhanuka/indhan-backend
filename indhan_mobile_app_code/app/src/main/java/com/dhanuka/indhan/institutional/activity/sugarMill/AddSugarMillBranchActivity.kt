package com.dhanuka.indhan.institutional.activity.sugarMill

import android.app.Activity
import android.app.Dialog
import android.content.Context
import android.content.Intent
import android.content.res.ColorStateList
import android.graphics.BitmapFactory
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.location.Address
import android.location.Geocoder
import android.os.Build
import android.os.Bundle
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.util.Patterns
import android.view.View
import android.view.Window
import android.view.inputmethod.InputMethodManager
import android.widget.ImageView
import com.bumptech.glide.Glide
import com.bumptech.glide.load.engine.DiskCacheStrategy
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllDistrictBean
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllStateBean
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.adapter.sugarMill.SugarMillAddContactAdapter
import com.dhanuka.indhan.institutional.model.RefreshEventModel
import com.dhanuka.indhan.institutional.model.sugarMill.*
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.*
import com.dhanuka.indhan.util.Currency
import kotlinx.android.synthetic.main.activity_add_sugar_mill_branch.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import org.json.JSONArray
import org.json.JSONObject
import pl.aprilapps.easyphotopicker.DefaultCallback
import pl.aprilapps.easyphotopicker.EasyImage
import pl.aprilapps.easyphotopicker.MediaFile
import pl.aprilapps.easyphotopicker.MediaSource
import java.io.File
import java.util.*

class AddSugarMillBranchActivity : BaseActivity(), SugarMillAddContactAdapter.OnClickItem {
    private var id: Long? = null
    private var easyImage: EasyImage? = null
    private var file: File? = null
    private var arrayListOfData: LinkedList<SugarMillAddContactModel> = LinkedList()
    private var sugarMillAddContactAdapter: SugarMillAddContactAdapter? = null
    private var sugarMillList: ArrayList<SugarMillHeadOfficeData> = ArrayList()
    private var companyTypeList: ArrayList<SugarMillCompanyData> = ArrayList()
    private var stateModelLis: ArrayList<AllStateBean.StateData> = ArrayList()
    private var districtList: ArrayList<AllDistrictBean.DistrictData> = ArrayList()
    private var levelData: ArrayList<String> = ArrayList()
    private var designationData: ArrayList<SugarMillDesignationData> = ArrayList()
    private var flDataList: ArrayList<SugarMillFlData> = ArrayList()
    private var selectSugarMill: NumberSpinner? = null
    private var selectCompanyType: NumberSpinner? = null
    private var stateSpinner: NumberSpinner? = null
    private var districtSpinner: NumberSpinner? = null
    private var selectFl: NumberSpinner? = null
    private var preference: Preference? = null
    private var userId = ""
    private var appDatabase: AppDatabase? = null
    private var state_id = "0"
    private var companyTypeId = 0
    private var sugarMillId = 0
    private var flName = ""
    private var flEcn = ""
    private var state = ""
    private var userType = ""
    private var district_id = ""
    private var district = ""
    private var isHeadOffice = true
    private var isSugarMill = true
    private var longitude: Double = 0.0
    private var latitude: Double = 0.0
    private var isBranch = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_sugar_mill_branch)
        preference = Preference(this)
        userId = preference?.getString("user_id") ?: ""
        userType = preference?.getString("user_type") ?: ""
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        rbSugarMill.isChecked = true
        id = System.currentTimeMillis()
        intent?.getStringExtra("parentId")?.let {
            sugarMillId = it.toInt()
        }
        intent?.getStringExtra("type")?.let {
            isSugarMill = !it.equals("1", ignoreCase = true)
        }
        intent?.getStringExtra("branch")?.let {
            isHeadOffice = false
            rbHeadOffice.isChecked = false
            rbHeadOffice.isEnabled = false
            rbBranch.isEnabled = false
            rbBranch.isChecked = true
            rbHeadOffice.setTextColor(resources.getColor(R.color.black))
            rbBranch.setTextColor(resources.getColor(R.color.black))
            isBranch = true
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                rbHeadOffice.buttonTintList = ColorStateList.valueOf(0xFFd3d3d3.toInt())
                rbBranch.buttonTintList = ColorStateList.valueOf(0xFF0CA160.toInt())
            } else {
                rbHeadOffice.setBackgroundColor(0xFFd3d3d3.toInt())
                rbBranch.setBackgroundColor(0xFF0CA160.toInt())
            }
        }
        setListeners()
        initCamera()
        setAdapter()
        getLocalData()
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtFilterSugarMill -> {
                hideKeyboardFrom(this, txtFilterSugarMill)
                selectSugarMill?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.txtCompanyType -> {
                hideKeyboardFrom(this, txtCompanyType)
                selectCompanyType?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.txtState -> {
                hideKeyboardFrom(this, txtState)
                stateSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.txtDistrict -> {
                hideKeyboardFrom(this, txtDistrict)
                districtSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.txtSelectFl -> {
                hideKeyboardFrom(this, txtSelectFl)
                selectFl?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.btnRbSubmit -> {
                showProgress()
                val newRecordContact: ArrayList<SugarMillAddContactModel> = ArrayList()
                for (indexOfArray in arrayListOfData.indices) {
                    if (validateFields(indexOfArray)) {
                        newRecordContact.add(arrayListOfData[indexOfArray])
                    } else {
                        break
                    }
                }
                GlobalScope.launch {
                    withContext(Dispatchers.Main) {
                        delay(200)
                        if (newRecordContact.size == 0) {
                            hideProgress()
                            makeToast("There is no contact added, please add contact.")
                        } else if (newRecordContact.size != 0) {
                            if (validateData()) {
                                callApiSubmitData(newRecordContact)
                            }else{
                                hideProgress()
                            }
                        }
                    }
                }
            }
            R.id.ivBack -> {
                hideKeyboardFrom(this, ivBack)
                finish()
            }
            R.id.txtAddMore -> {
                var someIndexIsEmpty = false
                for (position in arrayListOfData.indices) {
                    if (TextUtils.isEmpty(arrayListOfData[position].level)) {
                        someIndexIsEmpty = true
                        makeToast("Please select contact level.")
                        break
                    }

                    if (TextUtils.isEmpty(arrayListOfData[position].wa_number.trim())) {
                        someIndexIsEmpty = true
                        makeToast("Please select contact designation.")
                        break
                    }

                    if (TextUtils.isEmpty(arrayListOfData[position].contact_name.trim())) {
                        someIndexIsEmpty = true
                        makeToast("Please enter contact person name.")
                        break
                    }

                    if (TextUtils.isEmpty(arrayListOfData[position].mobile_number.trim())) {
                        someIndexIsEmpty = true
                        makeToast("Please enter contact mobile number.")
                        break
                    }

                    if (TextUtils.isEmpty(arrayListOfData[position].contact_email.trim())) {
                        someIndexIsEmpty = true
                        makeToast("Please enter contact email address.")
                        break
                    }

                    if (!Patterns.EMAIL_ADDRESS.matcher(arrayListOfData[position].contact_email.trim())
                            .matches()
                    ) {
                        someIndexIsEmpty = true
                        makeToast("Please enter valid contact email address.")
                        break
                    }

                    if (TextUtils.isEmpty(arrayListOfData[position].designation.trim())) {
                        someIndexIsEmpty = true
                        makeToast("Please enter contact email address.")
                        break
                    }
                }
                if (!someIndexIsEmpty) {
                    arrayListOfData.add(
                        arrayListOfData.size,
                        createEmptyAddContactModel()
                    )
                    addContactItem()
                }
            }
        }
    }

    private fun validateFields(indexOfArray: Int): Boolean {
        val subMenu = arrayListOfData[indexOfArray]
        if (!TextUtils.isEmpty(subMenu.level)) {
            if (!TextUtils.isEmpty(subMenu.wa_number)) {
                return if (!TextUtils.isEmpty(subMenu.contact_name)) {
                    if (!TextUtils.isEmpty(subMenu.wa_number)) {
                        if (!TextUtils.isEmpty(subMenu.mobile_number)) {
                            !TextUtils.isEmpty(subMenu.contact_email)
                        } else {
                            false
                        }
                    } else {
                        false
                    }
                } else {
                    false
                }
            } else {
                return false
            }
        } else {
            return false
        }
    }

    private fun callApiSubmitData(newRecordContact: ArrayList<SugarMillAddContactModel>) {
        val array = JSONArray()
        newRecordContact.forEach() {
            val jsonObject = JSONObject()
            jsonObject.accumulate("level", it.level.replace("Level ", ""))
            jsonObject.accumulate("designation", it.designation)
            jsonObject.accumulate("name", it.contact_name)
            jsonObject.accumulate("wa_number", it.wa_number)
            jsonObject.accumulate("mobile_number", it.mobile_number)
            jsonObject.accumulate("email", it.contact_email)
            array.put(jsonObject)
        }

        val requestData = AddSugarMillBranchModel(
            sugarmillId = id ?: 0L,
            apiToken = preference?.getString("token") ?: "",
            office_type = if (isHeadOffice) 1 else 2,
            sugarmill_type = if (isSugarMill) 1 else 2,
            parent_sugarmill_id = if (sugarMillId == 0) "0" else sugarMillId.toString(),
            name = etCompanyName.text.toString().trim(),
            turn_over = etTurnOVer.text.toString().trim(),
            land_area = etLandArea.text.toString().trim(),
            sugarmill_category_id = companyTypeId.toString(),
            sugarmill_category_type = "",
            state_id = state_id,
            district_id = district_id,
            district = district,
            state = state,
            address = etAddress.text.toString().trim(),
            latitude = latitude,
            longitude = longitude,
            image = file?.absolutePath,
            hitTime = System.currentTimeMillis(),
            fl_ecn = flEcn,
            fl_name = flName,
            fl_type = "bdo",
            status = 0,
            contact_data = array.toString()
        )

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase?.addSugarMillBranchDao()?.insert(requestData)
            }.run {
                delay(700)
                withContext(Dispatchers.Main) {
                    hideProgress()
                    if (CheckConnectionStatus.isInternetAvailable(this@AddSugarMillBranchActivity)) {
                        OfflineDatabase(this@AddSugarMillBranchActivity).callApiUpdateSugarMill()
                        makeToast("Your data will pushed in background, please check after some time.")
                    } else {
                        makeToast("You are offline, Your data will pushed in background when internet available.")
                    }
                    EventBus.getDefault().post(RefreshEventModel(data = 1, page = "sugar_mill"))
                    finish()
                }
            }
        }
    }

    private fun validateData(): Boolean {
        if (rgbType.checkedRadioButtonId == 0) {
            makeToast("please select SugarMill.")
            return false
        }
        if (rgbWhat.checkedRadioButtonId == 0) {
            makeToast("please select Head office or branch.")
            return false
        }

        if (rgbWhat.checkedRadioButtonId == R.id.rbHeadOffice) {
            if (TextUtils.isEmpty(etCompanyName.text.toString().trim())) {
                makeToast("Please enter company name.")
                return false
            }
            if (TextUtils.isEmpty(etTurnOVer.text.toString().trim())) {
                if (rgbType.checkedRadioButtonId == R.id.rbSugarMill) {
                    makeToast("Please enter annual procurement target in MT of SugarMill.")
                } else {
                    makeToast("Please enter turnover.")
                }
                return false
            }

            if (TextUtils.isEmpty(etLandArea.text.toString().trim())) {
                if (rgbType.checkedRadioButtonId == R.id.rbSugarMill) {
                    makeToast("Please enter Per Day Crushing capacity in MT of SugarMill.")
                } else {
                    makeToast("Please enter Per Day Crushing capacity in MT.")
                }
                return false
            }

            if (TextUtils.isEmpty(txtCompanyType.text.toString().trim())) {
                if (rgbType.checkedRadioButtonId == R.id.rbSugarMill) {
                    makeToast("Please select company type of SugarMill.")
                } else {
                    makeToast("Please select company type.")
                }
                return false
            }

            if (TextUtils.isEmpty(etAddress.text.toString().trim())) {
                if (rgbType.checkedRadioButtonId == R.id.rbSugarMill) {
                    makeToast("Please enter address of SugarMill.")
                } else {
                    makeToast("Please enter address.")
                }
                return false
            }
            if (TextUtils.isEmpty(txtState.text.toString().trim())) {
                if (rgbType.checkedRadioButtonId == R.id.rbSugarMill) {
                    makeToast("Please select state of SugarMill.")
                } else {
                    makeToast("Please select state.")
                }
                return false
            }

            if (TextUtils.isEmpty(txtState.text.toString().trim())) {
                if (rgbType.checkedRadioButtonId == R.id.rbSugarMill) {
                    makeToast("Please select district of SugarMill.")
                } else {
                    makeToast("Please select district.")
                }
                return false
            }
        } else {
            if (TextUtils.isEmpty(etCompanyName.text.toString().trim())) {
                makeToast("Please enter Branch name.")
                return false
            }

            if (TextUtils.isEmpty(etAddress.text.toString().trim())) {
                if (rgbType.checkedRadioButtonId == R.id.rbSugarMill) {
                    makeToast("Please enter address of SugarMill Branch.")
                } else {
                    makeToast("Please enter address Branch.")
                }
                return false
            }
            if (TextUtils.isEmpty(txtState.text.toString().trim())) {
                if (rgbType.checkedRadioButtonId == R.id.rbSugarMill) {
                    makeToast("Please select state of SugarMill Branch.")
                } else {
                    makeToast("Please select state Branch.")
                }
                return false
            }

            if (TextUtils.isEmpty(txtState.text.toString().trim())) {
                if (rgbType.checkedRadioButtonId == R.id.rbSugarMill) {
                    makeToast("Please select district of SugarMill Branch.")
                } else {
                    makeToast("Please select district Branch.")
                }
                return false
            }
        }

        if (!userType.contains("bdo", ignoreCase = true)) {
            if (TextUtils.isEmpty(txtSelectFl.text.toString().trim())) {
                val message = "Please assign FL for "
                if (rgbWhat.checkedRadioButtonId == R.id.rbHeadOffice) {
                    if (rgbType.checkedRadioButtonId == R.id.rbTeaEstate) {
                        message.plus("SugarMill")
                    } else {
                        message.plus("STG")
                    }
                } else {
                    if (rgbType.checkedRadioButtonId == R.id.rbTeaEstate) {
                        message.plus("SugarMill Branch")
                    } else {
                        message.plus("STG Branch")
                    }
                }
                makeToast(message)
                return false
            }
        }

        if (file == null) {
            makeToast("Please take an image.")
            return false
        }
        if (latitude == 0.0 && longitude == 0.0) {
            makeToast("Location latitude and longitude not found.")
            return false
        }
        return true
    }

    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@AddSugarMillBranchActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        easyImage?.handleActivityResult(
            requestCode,
            resultCode,
            data,
            this,
            object : DefaultCallback() {
                override fun onMediaFilesPicked(imageFiles: Array<MediaFile>, source: MediaSource) {
                    onPhotosReturned(imageFiles);
                }
            })
    }

    private fun onPhotosReturned(imageFiles: Array<MediaFile>) {
        if (imageFiles.isNotEmpty()) {
            ImagePickerUtil.onActivityResult2(10000, RESULT_OK, imageFiles[0])
        }
    }

    private fun setListeners() {
        longitude = (preference?.getString(Constants.longitude))?.toDoubleOrNull() ?: 0.0
        latitude = preference?.getString(Constants.latitude)?.toDoubleOrNull() ?: 0.0
        rgbWhat.setOnCheckedChangeListener { _, checkedId ->
            when (checkedId) {
                R.id.rbBranch -> {
                    isHeadOffice = false
                    checkVisibility()
                }
                R.id.rbHeadOffice -> {
                    isHeadOffice = true
                    checkVisibility()
                }
            }
        }

        rgbType.setOnCheckedChangeListener { _, checkedId ->
            when (checkedId) {
                R.id.rbStg -> {
                    isSugarMill = false
                    checkVisibility()
                }
                R.id.rbSugarMill -> {
                    isSugarMill = true
                    checkVisibility()
                }
            }
        }
        txtFilterSugarMill.setOnClickListener(this)
        txtCompanyType.setOnClickListener(this)
        txtState.setOnClickListener(this)
        txtDistrict.setOnClickListener(this)
        txtAddMore.setOnClickListener(this)
        txtSelectFl.setOnClickListener(this)
        btnRbSubmit.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        rlPicture.setOnClickListener(this)

        rlPicture.setOnClickListener {
            if (file == null) {
                longitude = (preference?.getString(Constants.longitude))?.toDoubleOrNull() ?: 0.0
                latitude = preference?.getString(Constants.latitude)?.toDoubleOrNull() ?: 0.0
                etAddress.setText(currentAddress(latitude, longitude) ?: "")
                PermissionsUtil.askPermissions(
                    this@AddSugarMillBranchActivity,
                    PermissionsUtil.CAMERA,
                    PermissionsUtil.STORAGE
                ) {
                    if (it) {
                        ImagePickerUtil.pickFromCamera2(
                            easyImage,
                            this@AddSugarMillBranchActivity,
                            { imagefile, _ ->
                                file = ImageUtils.compressToExistingFile(
                                    imagefile.absolutePath,
                                    imagefile
                                )
                                if(file == null){
                                    file = imagefile
                                }
                                val bitmap =
                                    BitmapFactory.decodeFile(file?.absolutePath)
                                imgPicture.setImageBitmap(bitmap)
                                imgCross.visibility = View.VISIBLE
                                imgCamera.visibility = View.GONE
                            },
                            ""
                        )
                    }
                }
            } else {
                showdialogone()
            }
        }

        imgCross.setOnClickListener {
            file = null
            imgCross.visibility = View.GONE
            imgCamera.visibility = View.VISIBLE
            imgPicture.setImageDrawable(null)
        }

        etTurnOVer.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {

            }

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
            }

            override fun afterTextChanged(s: Editable?) {
                if (!TextUtils.isEmpty(s)) {
                    "In Words - ${
                        Currency.convertToIndianCurrencySugarMill(
                            s.toString().trim()
                        )
                    }".also { txtTurnOver.text = it }
                } else {
                    txtTurnOver.text = "In Words -"
                }
            }

        })
    }

    private fun getLocalData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                if (!userType.contains("bdo", ignoreCase = true)) {
                    flDataList.clear()
                    (appDatabase?.flDataListSugarMillDao()?.getAllFlByManagerEcn(userId)
                        ?: ArrayList<SugarMillFlData>()).forEach() {
                        flDataList.add(it!!)
                    }
                }

                sugarMillList.clear()
                (appDatabase?.sugarMillHeadOfficeListDataDao()?.allSugarMill
                    ?: ArrayList<SugarMillHeadOfficeData>()).forEach() {
                    sugarMillList.add(it!!)
                }

                companyTypeList.clear()
                (appDatabase?.companyTypeDataListSugarMillDao()?.allCompanyType
                    ?: ArrayList<SugarMillCompanyData>()).forEach() {
                    companyTypeList.add(it!!)
                }

                stateModelLis.clear()
                val state = appDatabase?.stateDao()?.allStateList
                state?.let { stateModelLis.addAll(it) }

                levelData.clear()
                for (i in 0..9) {
                    levelData.add("Level ${i + 1}")
                }

                designationData.clear()
                (appDatabase?.designationDataListSugarMillDao()?.allDesignations)?.forEach() {
                    designationData.add(it!!)
                }

            }.run {
                withContext(Dispatchers.Main) {
                    addContactItem()
                    checkVisibility()
                    setDropDownData()
                }
            }
        }
    }

    private fun setDropDownData() {
        val SugarMillStringList = ArrayList<String>()
        sugarMillList.forEach() {
            it.name?.let { it1 -> SugarMillStringList.add(it1) }
        }

        if (sugarMillId != 0) {
            for (it in sugarMillList) {
                if (it.sugarmill_id == sugarMillId) {
                    txtFilterSugarMill.text = it.name
                    break
                }
            }
        }
        if(!isBranch) {
            selectSugarMill = NumberSpinner(this, SugarMillStringList, "Select Sugar Mill")
            selectSugarMill?.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    txtFilterSugarMill.text = item
                    sugarMillId = sugarMillList[position].sugarmill_id
                }
            })
        }
        val companyList = ArrayList<String>()
        companyTypeList.forEach() {
            it.company_type.let { it1 -> companyList.add(it1) }
        }
        selectCompanyType = NumberSpinner(this, companyList, "Select Company Type")
        selectCompanyType?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtCompanyType.text = item
                companyTypeId = companyTypeList[position].sugarmill_company_id ?: 0
            }
        })


        val stateListString = ArrayList<String>()
        stateModelLis.forEach() {
            it.state?.let { it1 -> stateListString.add(it1) }
        }
        stateSpinner = NumberSpinner(this, stateListString, "Select State")
        stateSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (stateModelLis.size > 0) {
                    state = item
                    state_id = stateModelLis[position].state_id ?: ""
                    txtState.text = item
                    GlobalScope.launch {
                        val districtListString = ArrayList<String>()
                        withContext(Dispatchers.IO) {
                            val updatedVal =
                                appDatabase?.districtDao()?.getAllDistrictList(state_id.toInt())
                            districtList.clear()
                            updatedVal?.let { districtList.addAll(it) }
                            updatedVal?.forEach() {
                                it.district?.let { it1 -> districtListString.add(it1) }
                            }
                        }.run {
                            withContext(Dispatchers.Main) {
                                setSpinnerDistrict(districtListString)
                            }
                        }
                    }
                }
            }
        })

        if (!userType.contains("bdo", ignoreCase = true)) {
            txtSelectFl.visibility = View.VISIBLE
            val flList = ArrayList<String>()
            flDataList.forEach() {
                it.name.let { it1 -> flList.add(it1) }
            }
            selectFl = NumberSpinner(this, flList, "Assign FL")
            selectFl?.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    if (flDataList.size > 0) {
                        txtSelectFl.text = item
                        flEcn = flDataList[position].ECN
                        flName = flDataList[position].name
                    }
                }
            })
        } else {
            txtSelectFl.visibility = View.GONE
            flEcn = preference?.getString("ecn") ?: ""
            flName = preference?.getString("Username") ?: ""
        }
    }

    private fun setSpinnerDistrict(districtListString: java.util.ArrayList<String>) {
        districtSpinner = NumberSpinner(this, districtListString, "Select District")
        districtSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (districtList.size > 0) {
                    district = item
                    district_id = (districtList[position].district_id ?: "").toString()
                    txtDistrict.text = item
                }
            }
        })
    }

    private fun checkVisibility() {
        if (isHeadOffice) {
            txtFilterSugarMill.visibility = View.GONE
            etCompanyName.hint = "Company Name"
            tvLabelAddNew.text = "Add New Sugar Mill"
            etTurnOVer.visibility = View.VISIBLE
            txtTurnOver.visibility = View.VISIBLE
            etLandArea.visibility = View.VISIBLE
            txtCompanyType.visibility = View.VISIBLE
        } else {
            txtFilterSugarMill.visibility = View.VISIBLE
            tvLabelAddNew.text = "Add Sugar Mill Branch"
            etCompanyName.hint = "Branch Name"
            etTurnOVer.visibility = View.GONE
            txtTurnOver.visibility = View.GONE
            etLandArea.visibility = View.GONE
            txtCompanyType.visibility = View.GONE
        }
    }

    private fun addContactItem() {
        if (arrayListOfData.size == 0) {
            arrayListOfData.add(0, createEmptyAddContactModel())
        }
        sugarMillAddContactAdapter?.updateListData(levelData, designationData)
        sugarMillAddContactAdapter?.notifyItemChanged(arrayListOfData.size)
    }

    private fun createEmptyAddContactModel(): SugarMillAddContactModel {
        return SugarMillAddContactModel(
            contact_id = System.currentTimeMillis(),
            contact_email = "",
            contact_name = "",
            designation = "",
            level = "",
            mobile_number = "",
            wa_number = ""
        )
    }

    private fun setAdapter() {
        sugarMillAddContactAdapter =
            SugarMillAddContactAdapter(this@AddSugarMillBranchActivity, arrayListOfData)
        rcvContact.adapter = sugarMillAddContactAdapter
        sugarMillAddContactAdapter?.setClickListener(this)
    }

    override fun onClickView(view: View, subMenu: SugarMillAddContactModel, position: Int) {
    }

    private fun hideKeyboardFrom(context: Context, view: View) {
        val imm: InputMethodManager =
            context.getSystemService(Activity.INPUT_METHOD_SERVICE) as InputMethodManager
        imm.hideSoftInputFromWindow(view.windowToken, 0)
    }

    private fun showdialogone() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_image)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(true)
        val msg = dialog.findViewById<View>(R.id.imgView) as ImageView
        val requestOptions = RequestOptions()
            .diskCacheStrategy(DiskCacheStrategy.NONE) // because file name is always same
            .skipMemoryCache(true)

        Glide.with(this)
            .load(file?.absoluteFile)
            .apply(requestOptions)
            .into(msg)
        dialog.show()
    }

    private fun currentAddress(currentLat: Double, currentLong: Double): String? {
        val sb: StringBuilder? = null
        var address: String? = null
        try {
            var geocoder: Geocoder? = null
            val addresses: List<Address>?
            geocoder = Geocoder(this, Locale.getDefault())
            addresses = geocoder.getFromLocation(currentLat, currentLong, 1)
            if (addresses != null && !addresses.isEmpty()) {
                // Here 1 represent max location result to returned, by documents it recommended 1 to 5
                val addresssss = addresses[0]
                address = addresssss.getAddressLine(0)
            }
        } catch (e: Exception) {
            e.printStackTrace()
        }

        return address
    }
}

