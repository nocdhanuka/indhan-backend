package com.dhanuka.indhan.NewTicketActivity

import android.Manifest
import android.app.Dialog
import android.content.pm.PackageManager
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import android.view.Window
import android.view.WindowManager
import androidx.core.app.ActivityCompat
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.LoginActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.*
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.DistrictSpinnerNewDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean
import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.SubmitTicketContracts
import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.SubmitTicketPresenterImpl
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.TehsilSpinnerNewDialog
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel
import com.dhanuka.indhan.DataBase.DatabaseHandler
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.MenuScreenActivity.Model.AllLocation
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.*
import com.dhanuka.indhan.Model.LanguageModel
import com.dhanuka.indhan.Model.TerritoryResponse
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.LanguageSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.bdoddlist.BdoDdListBean
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.OfflineTicketModel
import com.dhanuka.indhan.util.*
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_new_ticket.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.joda.time.DateTime
import rx.Single
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*

/**
 * Created by DARS on 02/01/2022.
 */

class NewTicketActivity : BaseActivity(), SubmitTicketContracts.ISubmitTicketView {

    override fun setVillageList(items: MyVillageBean) {
    }

    private var Token = ""
    private lateinit var appDatabase: AppDatabase
    private lateinit var msubmitTicketPresenter: SubmitTicketContracts.SubmitTicketPresenter
    private lateinit var fusedLocationClient: FusedLocationProviderClient

    private var prodCropResponseModel: ArrayList<CropBean> = ArrayList();
    private var dataSourceRecordResponseModel = ArrayList<DataSourceBean>()
    private var languageList = ArrayList<LanguageModel>()
    private var stateRecordResponseModel = ArrayList<AllStateBean.StateData>()

    private var districtRecordResponseModel = ArrayList<AllDistrictBean.DistrictData>()
    private var tehsilRecordResponseModel = ArrayList<AllTehsilBean.TehsilData>()
    private var product1RecordResponseModel = ArrayList<ProductBean>()
    private var product2RecordResponseModel = ArrayList<ProductBean>()
    private var product3RecordResponseModel = ArrayList<ProductBean>()
    private var crop1RecordResponseModel = ArrayList<CropBean>()
    private var crop2RecordResponseModel = ArrayList<CropBean>()
    private var crop3RecordResponseModel = ArrayList<CropBean>()

    private var territoryList = ArrayList<TerritoryResponse.TerritoryData>()
    private var territory = java.util.ArrayList<String>()

    private var product1Name: String = ""
    private var product2Name: String = ""
    private var product3Name: String = ""

    private var dataSourceId: Int = 0
    private var stateId: Int = 0
    private var product1Id: Int = 0
    private var teritoryId: Int = 0
    private var product2Id: Int = 0
    private var product3Id: Int = 0
    private var crop1Id: Int = 0
    private var crop2Id: Int = 0
    private var crop3Id: Int = 0
    private var districtId: Int = 0
    private var tehsilId: Int = 0
    private var languageId: Int = 0
    private var demoId = 0
    private var villageId: Int = 0

    private var demoId1 = ""
    private var mLatitude: String = ""
    private var mLongitude: String = ""

    private var proCrop2 = false
    private var proCrop3 = false

    private lateinit var dataSourceName: String
    private  var teritory: String = ""
    private lateinit var stateName: String
    private lateinit var crop1Name: String
    private lateinit var crop2Name: String
    private lateinit var crop3Name: String
    private lateinit var districtName: String
    private lateinit var tehsilName: String

    private lateinit var dataSourceSpinnerDialog: DataSourceSpinnerDialog
    private lateinit var languageSpinnerDialog: LanguageSpinner
    private lateinit var stateSpinnerDialog: StateSpinnerNewDialog
    private lateinit var districtSpinnerDialog: DistrictSpinnerNewDialog
    private lateinit var tehsilSpinnerDialog: TehsilSpinnerNewDialog
    private lateinit var productSpinnerDialog: ProductSpinnerNewDialog
    private lateinit var product2SpinnerDialog: ProductSpinnerNewDialog
    private lateinit var product3SpinnerDialog: ProductSpinnerNewDialog
    private lateinit var cropSpinnerDialog: CropSpinnerNewDialog
    private lateinit var crop2SpinnerDialog: CropSpinnerNewDialog
    private lateinit var crop3SpinnerDialog: CropSpinnerNewDialog
    private var selectTerritory: NumberSpinner? = null

    private lateinit var db: DatabaseHandler
    private lateinit var preference: Preference
    private lateinit var villageSpinnerDialog: VillageSpinnerNewDialog
    private lateinit var demoRequestSpinnerDialog: DemoRequestListSpinnerDialog

    private lateinit var allLocation: AllLocation
    private lateinit var ddNameDialog: DdNameListDialog

    private var villageList: ArrayList<AllVillageBean.VillageData> = ArrayList()
    private var demoList: ArrayList<DemoRequestList> = ArrayList()

    var userType: String = ""
    var ddId = ""
    var ddName = ""

    var ddList: ArrayList<BdoDdListBean.Data> = ArrayList()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_new_ticket)

        db = DatabaseHandler(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        window.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
//        txtQuestionDd
//        checkboxDd
//        llCheckBoxDd
        preference = Preference(this)
        userType = preference.getString("user_type")
        if (userType.equals("BDO", false)) {
            getDDListFromDB()
            llDd.visibility = View.VISIBLE
        } else {
            llDd.visibility = View.GONE
        }

        if (userType.equals("dhanuka_doctor")) {
            val ddPermission = preference.getString("activity_permission")
            if (ddPermission == "1") {
                llCheckBoxDd.visibility = View.VISIBLE
            } else {
                llCheckBoxDd.visibility = View.GONE
            }
        } else {
            llCheckBoxDd.visibility = View.GONE
        }

        btnRbSubmit.setOnClickListener(this)
        tvReAddNew1.setOnClickListener(this)
        tvReAddNew2.setOnClickListener(this)
        tvReRemove1.setOnClickListener(this)
        tvReRemove2.setOnClickListener(this)
        ivBackHome.setOnClickListener(this)

        tvReSPAddNew1.setOnClickListener(this)
        tvRemoveSP2.setOnClickListener(this)
        spinnerLanguage.setOnClickListener(this)
        tvRemoveSP3.setOnClickListener(this)
        tvRemoveSP4.setOnClickListener(this)
        tvRemoveSP5.setOnClickListener(this)
        tvRemoveSP6.setOnClickListener(this)
        spinnerDataSource.setOnClickListener(this)
        spinnerState.setOnClickListener(this)
        spinnerDistrict.setOnClickListener(this)
        spinnerTerritory.setOnClickListener(this)
        spinnerTehsil.setOnClickListener(this)
        spinnerProduct1.setOnClickListener(this)
        spinnerProduct2.setOnClickListener(this)
        spinnerProduct3.setOnClickListener(this)
        spinnerCrop1.setOnClickListener(this)
        spinnerCrop2.setOnClickListener(this)
        spinnerCrop3.setOnClickListener(this)
        spinnerVillage.setOnClickListener(this)
        spinnerDemoRequestId.setOnClickListener(this)
        llDd.setOnClickListener(this)

        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)
        getLocation()
        languageSpinnerDialog = LanguageSpinner(this, languageList, "Select or Search Language")
        languageSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                languageId = languageList.get(position).id.toInt()
                spinnerLanguage.text = languageList.get(position).language
            }
        })
        dataSourceSpinnerDialog = DataSourceSpinnerDialog(
            this,
            dataSourceRecordResponseModel,
            "Select or Search Data Source"
        )
        stateSpinnerDialog =
            StateSpinnerNewDialog(this, stateRecordResponseModel, "Select or Search State")
        districtSpinnerDialog =
            DistrictSpinnerNewDialog(this, districtRecordResponseModel, "Select or Search District")
        selectTerritory =
            NumberSpinner(this, territory, "Search or Select Territory")
        tehsilSpinnerDialog =
            TehsilSpinnerNewDialog(this, tehsilRecordResponseModel, "Select or Search Tehsil")
        productSpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        product2SpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        product3SpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        cropSpinnerDialog =
            CropSpinnerNewDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        crop2SpinnerDialog =
            CropSpinnerNewDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        crop3SpinnerDialog =
            CropSpinnerNewDialog(this, crop1RecordResponseModel, "Select or Search Crop")
        villageSpinnerDialog = VillageSpinnerNewDialog(this, villageList, "Select Village")
        Token = Preference.getInstance(this).getString("token")

        msubmitTicketPresenter = SubmitTicketPresenterImpl(this)
        demoRequestSpinnerDialog =
            DemoRequestListSpinnerDialog(this, demoList, "Select Demo request")

        getDemoRequestFromDb()
        geDataSourceListFromDb()
        getStateListFromDb()
        updateLanguageList()
        getCropListFromDb()


        demoRequestSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (position == demoList.size) {
                    spinnerDemoRequestId.text = "N/A"
                    demoId = 1
                } else {
                    demoId1 = demoList[position].demo_request_id.toString()
                    spinnerDemoRequestId.text = item
                }
            }
        })
    }

    private fun updateLanguageList() {
        languageList.clear()
        languageList = DatabaseManager.getLanguageList(this)
        languageList.let {
            languageSpinnerDialog.updateList(it)
        }
    }

    private fun checkValue(id: Int): Boolean {
        product1RecordResponseModel.forEach {
            if (id == it.crop_id.toInt()) {
                return true
            }
        }
        return false
    }

    private fun getLocation() {
        PermissionsUtil.askPermission(this, PermissionsUtil.LOCATION) {
            if (it) {
                onLocationPermissionGranted()
            }
        }
    }

    private fun onLocationPermissionGranted() {
        if (ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.ACCESS_FINE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.ACCESS_COARSE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            return
        }
        fusedLocationClient.lastLocation
            .addOnSuccessListener(this) { location ->
                if (location != null) {
                    mLatitude = location.latitude.toString()
                    mLongitude = location.longitude.toString()
                }
            }


    }

    private fun updateProductBeanList(id: Int, position: Int) {
        val temp = ArrayList<ProductBean>()
        prodCropResponseModel.forEach {
            if (it.crop_id.equals(id.toString())) {
                val product = ProductBean()
                product.product_id = (it.product_id)
                product.product = it.product
                temp.add(product)
            }
        }

        when (position) {
            1 -> {
                product1RecordResponseModel.clear()
                product1RecordResponseModel.addAll(temp)
                productSpinnerDialog.updatelist(product1RecordResponseModel)
            }
            2 -> {
                product2RecordResponseModel.clear()
                product2RecordResponseModel.addAll(temp)
                product2SpinnerDialog.updatelist(product2RecordResponseModel)
            }
            3 -> {
                product3RecordResponseModel.clear()
                product3RecordResponseModel.addAll(temp)
                product3SpinnerDialog.updatelist(product3RecordResponseModel)
            }
        }
    }

    override fun onClick(v: View) {
        when (v.id) {
            R.id.spinnerDataSource -> {
                if (!dataSourceSpinnerDialog.isEnable)
                    dataSourceSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerDemoRequestId -> {
                if (!demoRequestSpinnerDialog.isEnable)
                    demoRequestSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerState -> {
                if (!stateSpinnerDialog.isEnable)
                    stateSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerDistrict -> {
                if (!districtSpinnerDialog.isEnable)
                    districtSpinnerDialog.showSpinerDialog()
            }

            R.id.spinnerTerritory -> {
                if (selectTerritory?.isEnable != true) {
                    selectTerritory?.showSpinerDialog()
                }
            }

            R.id.spinnerTehsil -> {
                if (!tehsilSpinnerDialog.isEnable)
                    tehsilSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerProduct1 -> {
                if (!productSpinnerDialog.isEnable)
                    productSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerProduct2 -> {
                if (!product2SpinnerDialog.isEnable && !product1Name.isNullOrEmpty())
                    product2SpinnerDialog.showSpinerDialog()
                else
                    makeToast("Select Product one")
            }
            R.id.spinnerProduct3 -> {
                if (!product3SpinnerDialog.isEnable && !product2Name.isNullOrEmpty())
                    product3SpinnerDialog.showSpinerDialog()
                else
                    makeToast("Select Product 1 and Product 2")
            }
            R.id.spinnerCrop1 -> {
                if (!cropSpinnerDialog.isEnable)
                    cropSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerVillage -> {
                if (!villageSpinnerDialog.isEnable)
                    villageSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerCrop2 -> {
                if (!crop2SpinnerDialog.isEnable)
                    crop2SpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerCrop3 -> {
                if (!crop3SpinnerDialog.isEnable)
                    crop3SpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerLanguage -> {
                if (!languageSpinnerDialog.isEnable)
                    languageSpinnerDialog.showSpinerDialog()
            }
            R.id.tvReSPAddNew1 -> {
                if (llSp2.visibility == View.GONE) {
                    llSp2.visibility = View.VISIBLE
                } else if (llSp3.visibility == View.GONE) {
                    llSp3.visibility = View.VISIBLE
                } else if (llSp4.visibility == View.GONE) {
                    llSp4.visibility = View.VISIBLE
                } else if (llSp5.visibility == View.GONE) {
                    llSp5.visibility = View.VISIBLE
                } else if (llSp6.visibility == View.GONE) {
                    llSp6.visibility = View.VISIBLE
                }

                if (llSp2.visibility == View.VISIBLE && llSp3.visibility == View.VISIBLE && llSp4.visibility == View.VISIBLE
                    && llSp5.visibility == View.VISIBLE && llSp6.visibility == View.VISIBLE
                ) {
                    tvReSPAddNew1.visibility = View.GONE
                }
            }
            R.id.tvRemoveSP2 -> {
                etRetParName2.setText("")
                etRetParNum2.setText("")
                llSp2.visibility = View.GONE
                tvReSPAddNew1.visibility = View.VISIBLE
            }
            R.id.tvRemoveSP3 -> {
                etRetParName3.setText("")
                etRetParNum3.setText("")
                llSp3.visibility = View.GONE
                tvReSPAddNew1.visibility = View.VISIBLE
            }
            R.id.tvRemoveSP4 -> {
                etRetParName4.setText("")
                etRetParNum4.setText("")
                llSp4.visibility = View.GONE
                tvReSPAddNew1.visibility = View.VISIBLE
            }
            R.id.tvRemoveSP5 -> {
                etRetParName5.setText("")
                etRetParNum5.setText("")
                llSp5.visibility = View.GONE
                tvReSPAddNew1.visibility = View.VISIBLE
            }
            R.id.tvRemoveSP6 -> {
                etRetParName6.setText("")
                etRetParNum6.setText("")
                llSp6.visibility = View.GONE
                tvReSPAddNew1.visibility = View.VISIBLE
            }

            R.id.ivBackHome -> {
                onBackPressed()
            }

            R.id.tvReAddNew1 -> {
                llView2.visibility = View.VISIBLE
                tvReAddNew1.visibility = View.GONE
                tvReAddNew2.visibility = View.VISIBLE
                proCrop2 = true
                spinnerProduct2.text = "Select Product"
                spinnerCrop2.text = "Select Crop"
                /*crop2Id = 0*/
                product2Id = 0
            }
            R.id.tvReRemove1 -> {
                llView2.visibility = View.GONE
                tvReAddNew1.visibility = View.VISIBLE
                tvReAddNew2.visibility = View.GONE
                crop2Id = 0
                product2Id = 0
                proCrop2 = false
            }
            R.id.tvReAddNew2 -> {
                llView3.visibility = View.VISIBLE
                tvReAddNew2.visibility = View.GONE
                proCrop3 = true
                spinnerProduct3.text = "Select Product"
                spinnerCrop3.text = "Select Crop"
                /*crop3Id = 0*/
                product3Id = 0
            }
            R.id.tvReRemove2 -> {
                llView3.visibility = View.GONE
                tvReAddNew2.visibility = View.VISIBLE
                crop3Id = 0
                product3Id = 0
                proCrop3 = false
            }
            R.id.llDd -> {
                if (ddList.size > 0) {
                    ddNameDialog = DdNameListDialog(
                        this,
                        ddList,
                        "Select or Search DD Name",
                        object : OnGetId {
                            override fun onClick(item: String, position: Int, id: String) {
                                spinnerDD.text = item
                                ddId = id
                                ddName = item
                            }

                        })
                    ddNameDialog.showSpinerDialog()
                }
            }
            R.id.btnRbSubmit -> {
                if (languageId == 0) {
                    makeToast("Please Select Language.")
                    return
                }
                val dDTicketRole = when {
                    checkboxDd.isChecked -> "1"
                    else -> "0"
                }
                if (dataSourceId != 0) {
                    if (stateId != 0) {
                        if (districtId != 0) {
                            if (tehsilId != 0) {
                                if (villageId != 0) {
                                    if (product1Id != 0) {
                                        if (crop1Id != 0) {
                                            val query = HashMap<String, Any>()
                                            query.put("api_token", Token)
                                            query.put("dsId", dataSourceId)
                                            query.put("sId", stateId)
                                            query.put("dId", districtId)
                                            query.put("tId", tehsilId)
                                            query.put("language_id", languageId)
                                            query.put("product_1", product1Id)
                                            query.put("crop_1", crop1Id)
                                            query.put("territory_id", teritoryId)
                                            query.put("territory", teritory)
                                            if (userType.equals("BDO", false)) {
                                                query.put("dd_id", ddId)
                                            }
                                            /*Recently Change By Aman*/
                                            if (proCrop2 /*&& crop2Id != 0*/ && product2Id != 0) {
                                                query.put("product_2", product2Id)
                                                query.put("crop_2", crop2Id)
                                            } else {
                                                query.put("product_2", "")
                                                query.put("crop_2", "")
                                            }
                                            if (proCrop3 /* && crop3Id != 0*/ && product3Id != 0) {
                                                query.put("product_3", product3Id)
                                                query.put("crop_3", crop3Id)
                                            } else {
                                                query.put("product_3", "")
                                                query.put("crop_3", "")
                                            }
                                            query.put(
                                                "village",
                                                spinnerVillage.text.toString().trim()
                                            )
                                            query.put("village_id", villageId)
                                            query.put(
                                                "expected_farmers",
                                                etRetExpFarmer.text.toString().trim()
                                            )
                                            query.put(
                                                "expected_pda",
                                                etRetExpPDA.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_name_1",
                                                etRetParName.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_number_1",
                                                etRetParNum.text.toString().trim()
                                            )
                                            query.put("demo_request_id", demoId1)
                                            query.put("user_farmer", demoId)
                                            query.put(
                                                "sp_name_2",
                                                etRetParName2.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_number_2",
                                                etRetParNum2.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_name_3",
                                                etRetParName3.text.toString().trim()
                                            )
                                            query.put(
                                                "dd_ticket_role", dDTicketRole
                                            )
                                            query.put(
                                                "sp_number_3",
                                                etRetParNum3.text.toString().trim()
                                            )
                                            query.put(
                                                "sp_name_4",
                                                etRetParName4.text.toString().trim()
                                            )
                                            query["sp_number_4"] =
                                                etRetParNum4.text.toString().trim()
                                            query["sp_name_5"] =
                                                etRetParName5.text.toString().trim()
                                            query["sp_number_5"] =
                                                etRetParNum5.text.toString().trim()
                                            query["sp_name_6"] =
                                                etRetParName6.text.toString().trim()
                                            query["sp_number_6"] =
                                                etRetParNum6.text.toString().trim()
                                            query["latitude"] = mLatitude
                                            query["longitude"] = mLongitude
                                            if (checkSpNumber()) {
                                                if (CheckConnectionStatus.isInternetAvailable(this)) {
                                                    msubmitTicketPresenter.onSubmitClick(query)
                                                    logEventForCreateTicket(
                                                        "",
                                                        Gson().toJson(query),
                                                        true
                                                    )
                                                } else {
                                                    saveTicketOffline(query)
                                                    makeToast("No Internet Connection. Your data will be synced when online.")
                                                }
                                            } else {
                                            }
                                        } else {
                                            makeToast(resources.getString(R.string.txt_crop_source))
                                        }
                                    } else {
                                        makeToast(resources.getString(R.string.txt_product_source))
                                    }
                                } else {
                                    makeToast("Please select village")
                                }
                            } else {
                                makeToast(resources.getString(R.string.txt_tehsil_source))
                            }
                        } else {
                            makeToast(resources.getString(R.string.txt_district_source))
                        }
                    } else {
                        makeToast(resources.getString(R.string.txt_state_source))
                    }
                } else {
                    makeToast(resources.getString(R.string.txt_data_source))
                }
            }
        }
    }

    private fun logEventForCreateTicket(s: String, query: String, b: Boolean) {
        val logModel = LogModel()
        logModel.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
        val data = "Create Ticket data : $query"
        logModel.apiData = data
        logModel.apiName =
            when {
                b -> "Api Name : v2/newTicketSubmit  ${if (TextUtils.isEmpty(s)) "Api Called" else s} "
                TextUtils.isEmpty(s) -> "Api Called"
                else -> s
            }
        val userData = (preference.getString("user_id") + "_"
                + preference.getString("ecn") + "_"
                + preference.getString("user_type") + "_"
                + preference.getString("Username") + "_"
                + preference.getString("designation"))
        logModel.userData = "\nUSER DATA : -> $userData"
        AppLogFile.writeLineInFile(Gson().toJson(logModel), preference.getString("ecn"))
    }

    private fun checkSpNumber(): Boolean {
        val num: String = etRetParNum.text.toString().trim()
        val num2: String = etRetParNum2.text.toString().trim()
        val num3: String = etRetParNum3.text.toString().trim()
        val num4: String = etRetParNum4.text.toString().trim()
        val num5: String = etRetParNum5.text.toString().trim()
        val num6: String = etRetParNum6.text.toString().trim()
        if (!num.isNullOrEmpty()) {
            if (num.length in 1..9) {
                makeToast("Please Enter Valid Number")
                return false
            }
        }
        if (!num2.isNullOrEmpty()) {
            if (num2.length in 1..9) {
                makeToast("Please Enter Valid Number")
                return false
            }
        }
        if (!num3.isNullOrEmpty()) {
            if (num3.length in 1..9) {
                makeToast("Please Enter Valid Number")
                return false
            }
        }
        if (!num4.isNullOrEmpty()) {
            if (num4.length in 1..9) {
                makeToast("Please Enter Valid Number")
                return false
            }
        }
        if (!num5.isNullOrEmpty()) {
            if (num5.length in 1..9) {
                makeToast("Please Enter Valid Number")
                return false
            }
        }
        if (!num6.isNullOrEmpty()) {
            if (num6.length in 1..9) {
                makeToast("Please Enter Valid Number")
                return false
            }
        }

        return true

    }

    override fun setItems(items: SubmitTicketBean) {
        when (items.status_code) {
            200 -> {
                db.deleteAll()
                showdialog(items.data)
                logEventForCreateTicket("Api Success", Gson().toJson(items), false)
            }
            409 -> {
                val preference: Preference = Preference.getInstance(this)
                preference.putString("ticketId", "")
                preference.putString("ticketTime", "")
                preference.putBolean("isLogin", false)
                preference.putString("token", "")
                logEventForCreateTicket("Session out", Gson().toJson(items), false)
                launchIntent(LoginActivity::class.java, false)
                finishAffinity()
            }
            else -> {
                makeToast(items.message)
                logEventForCreateTicket("Api error | ${items.message}", Gson().toJson(items), false)
            }
        }
    }


    private fun geDataSourceListFromDb() {
        Single.fromCallable {
            dataSourceRecordResponseModel =
                appDatabase.dataSourceDao().allDataSourceList as ArrayList<DataSourceBean>
            dataSourceSpinnerDialog = DataSourceSpinnerDialog(
                this,
                dataSourceRecordResponseModel,
                "Select or Search Data Source"
            )
            manageDataSourceSpinnerClick()
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun manageDataSourceSpinnerClick() {
        dataSourceSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerDataSource.text = item
                dataSourceId = dataSourceRecordResponseModel[position].dsID!!
                dataSourceName = dataSourceRecordResponseModel[position].item.toString()
                when {
                    dataSourceName.equals("IFC", ignoreCase = true) -> {
                        spinnerDD.text = "My Self"
                        ddId = "0"
                        ddName = "My Self"
                        llDd.isEnabled = false
                    }
                    dataSourceName.equals("Field Day", ignoreCase = true) -> {
                        llDd.isEnabled = true
                        tvReAddNew1.visibility = View.GONE
                        llView2.visibility = View.GONE
                        llView3.visibility = View.GONE
                        demoRequestView.visibility = View.VISIBLE
                        spinnerDemoRequestId.visibility = View.VISIBLE
                        viewOne.visibility = View.VISIBLE

                        product2Id = 0
                        crop2Id = 0
                        product3Id = 0
                        crop3Id = 0
                        spinnerProduct2.text = "Select Product"
                        spinnerCrop2.text = "Select Crop"
                        spinnerProduct3.text = "Select Product"
                        spinnerCrop3.text = "Select Crop"
                    }
                    else -> {
                        llDd.isEnabled = true
                        tvReAddNew1.visibility = View.VISIBLE
                        demoRequestView.visibility = View.GONE
                        spinnerDemoRequestId.visibility = View.GONE
                        viewOne.visibility = View.GONE
                    }
                }
            }
        })
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private fun showdialog(ticketNumberDisp: String?) {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(android.graphics.Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_ticket_number)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val ticketNumber = dialog.findViewById<View>(R.id.tvMeTicketNumber) as Medium
        ticketNumber.text = ticketNumberDisp
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
            finish()
        }
        if (!isFinishing)
            dialog.show()
    }

    private fun getDemoRequestFromDb() {
        Single.fromCallable {
            demoList =
                appDatabase.demoRequestListDao().allDemoRequestList as ArrayList<DemoRequestList>
            demoRequestSpinnerDialog.updatelist(demoList)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getDDListFromDB() {
        Single.fromCallable {
            ddList = appDatabase.ddListDao().allDDList as ArrayList<BdoDdListBean.Data>
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getStateListFromDb() {
        Single.fromCallable {
            stateRecordResponseModel.clear()
            stateRecordResponseModel =
                appDatabase.stateDao().allStateList as ArrayList<AllStateBean.StateData>
            stateSpinnerDialog =
                StateSpinnerNewDialog(this, stateRecordResponseModel, "Select or Search State")
            stateSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    runOnUiThread {
                        spinnerState.text = item
                        stateId = stateRecordResponseModel[position].state_id!!.toInt()
                        stateName = stateRecordResponseModel[position].state
                        if (stateId != 0) {
                            getDistrictListFromDb(stateId)
                        }
                    }
                }
            })
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getDistrictListFromDb(state_id: Int) {
        spinnerDistrict.text = "Select District"
        spinnerTehsil.text = "Select Tehsil"
        spinnerTerritory.text = "Select Territory"
        Single.fromCallable {
            tehsilId = 0
            districtRecordResponseModel.clear()
            districtRecordResponseModel = appDatabase.districtDao()
                .getAllDistrictList(state_id) as ArrayList<AllDistrictBean.DistrictData>
            districtSpinnerDialog = DistrictSpinnerNewDialog(
                this,
                districtRecordResponseModel,
                "Select or Search District"
            )
            districtSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    runOnUiThread {
                        spinnerDistrict.text = item
                        districtId = districtRecordResponseModel[position].district_id!!.toInt()
                        districtName = districtRecordResponseModel[position].district
                        if (districtId != 0) {
                            getTerritoryListFromDb(districtId)
                            getTehsilListFromDb(districtId)
                        }
                    }

                }
            })
            tehsilRecordResponseModel.clear()
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getTehsilListFromDb(districtId: Int) {
        spinnerTehsil.text = "Select Tehsil"

        Single.fromCallable {
            tehsilRecordResponseModel.clear()
            tehsilRecordResponseModel = appDatabase.tehsilDao()
                .getAllTehsilList(districtId) as ArrayList<AllTehsilBean.TehsilData>
            tehsilSpinnerDialog =
                TehsilSpinnerNewDialog(this, tehsilRecordResponseModel, "Select or Search Tehsil")
            tehsilSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    spinnerTehsil.text = item
                    tehsilId = tehsilRecordResponseModel[position].tehsil_id.toInt()
                    spinnerVillage.text = ""
                    villageId = 0
                    tehsilName = tehsilRecordResponseModel[position].tehsil
                    getVillageList(tehsilId)
                }
            })
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
        }.subscribe()
    }

    private fun getTerritoryListFromDb(districtId: Int) {
        spinnerTerritory.text = "Select Territory"
        territoryList.clear()
        territory.clear()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                territoryList = appDatabase.territoryDao()
                    .getAllTerritoryList(districtId) as ArrayList<TerritoryResponse.TerritoryData>
            }.run {
                withContext(Dispatchers.Main) {
                    setTerritoryData()
                }
            }
        }
    }


    private fun setTerritoryData() {
        territoryList.forEach {
            territory.add(it.territory)
        }
        selectTerritory =
            NumberSpinner(this, territory, "Search or Select Territory")
        selectTerritory?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerTerritory.text = item
                teritory = item
                val territoryObject: TerritoryResponse.TerritoryData =
                    territoryList.find { it.territory == item }!!
                teritoryId = territoryObject.territory_id
            }
        })
    }

    private fun getVillageList(tehsil_id: Int) {
        Single.fromCallable {
            villageList = appDatabase.villageDao()
                .getAllVillageList(tehsil_id) as ArrayList<AllVillageBean.VillageData>
            villageSpinnerDialog.updatelist(villageList)
            villageSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    spinnerVillage.text = villageList[position].village.toString()
                    villageId = villageList[position].village_id.toInt()
                }
            })
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getCropListFromDb() {
        Single.fromCallable {
            prodCropResponseModel = appDatabase.cropDao().allCropList as ArrayList<CropBean>
            crop1RecordResponseModel = appDatabase.cropDao().allCropList as ArrayList<CropBean>

            cropSpinnerDialog =
                CropSpinnerNewDialog(this, crop1RecordResponseModel, "Select or Search Crop")
            cropSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    spinnerCrop1.text = item
                    crop1Id = crop1RecordResponseModel[position].crop_id.toInt()
                    crop2Id = crop1RecordResponseModel[position].crop_id.toInt()
                    crop3Id = crop1RecordResponseModel[position].crop_id.toInt()
                    crop1Name = crop1RecordResponseModel[position].crop
//                    updateProductBeanList(crop1Id, 1)
                    product1Id = 0;
                    product1Name = ""
                    product3Id = 0
                    product3Name = ""
                    product2Id = 0
                    product2Name = ""
                    spinnerProduct1.text = "Product Name"
                    spinnerProduct2.text = "Product Name"
                    spinnerProduct3.text = "Product Name"
                    getProductListFromDb(crop1Id)
//                    filterOther(1)
                }
            })
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getProductListFromDb(cropId: Int) {
        Single.fromCallable {
            product1RecordResponseModel =
                appDatabase.productDao().getAllProductList(cropId) as ArrayList<ProductBean>
            product2RecordResponseModel = product1RecordResponseModel
            product3RecordResponseModel = product1RecordResponseModel
            productSpinnerDialog.updatelist(product1RecordResponseModel)
            product2SpinnerDialog.updatelist(product2RecordResponseModel)
            product3SpinnerDialog.updatelist(product3RecordResponseModel)
            setProductSpinnerDialog()
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun setProductSpinnerDialog() {
        productSpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        productSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerProduct1.text = item
                product1Id = product1RecordResponseModel[position].product_id.toInt()
                product1Name = product1RecordResponseModel[position].product
            }
        })
        product2SpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        product2SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerProduct2.text = item
                product2Id = product1RecordResponseModel[position].product_id.toInt()
                product2Name = product1RecordResponseModel[position].product
            }
        })
        product3SpinnerDialog =
            ProductSpinnerNewDialog(this, product1RecordResponseModel, "Select or Search Product")
        product3SpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerProduct3.text = item
                product3Id = product1RecordResponseModel[position].product_id.toInt()
                product3Name = product1RecordResponseModel[position].product
            }
        })
    }

    private fun saveTicketOffline(hashMap: HashMap<String, Any>) {
        Single.fromCallable {

            val list = appDatabase.ticketHistoryDao().allTicketHistory
            for (i in list.indices)
                appDatabase.ticketHistoryDao().updateTicketStatus(list[i].id, "INACTIVE")

            val ticketId = "SV2${randomInt(2)}${randomInt(2)}${randomInt(2)}${randomInt(1)}"
            hashMap["ticket_no"] = ticketId
            val json = Gson().toJson(hashMap)
            val offlineModel = OfflineTicketModel()
            offlineModel.request = json
            appDatabase.createOfflineTicketDao().insert(offlineModel)
            val datum = TicketHistoryModel.Datum()
            datum.isOffline = true
            datum.ticket = ticketId
            datum.ddTicketRole = hashMap["dd_ticket_role"].toString()
            datum.datetime = getCurrentDate("yyyy-MM-dd hh:mm:ss")
            datum.datetimeFormat = getCurrentDate("EEEE, MMMM dd yyyy, hh:mm a")
            datum.description = "$dataSourceName, $stateName, $districtName, $tehsilName"
            datum.totalFarmers = 0
            datum.totalValidFarmers = 0
            datum.teritoryId = hashMap["territory_id"].toString()
            datum.territory = hashMap["territory"].toString()
            datum.dataSource = dataSourceName
            datum.state = stateName
            datum.district = districtName
            datum.tehsil = tehsilName
            datum.village = spinnerVillage.text.toString()
            datum.demo_request_id = ""
            datum.product1 = product1Name
            datum.product2 = product2Name
            datum.product3 = product3Name
            datum.crop1 = crop1Name
            datum.crop2 = crop1Name
            datum.crop3 = crop1Name
            datum.isTicketClosed = false
            datum.expectedFarmers =
                if (etRetExpFarmer.text.toString() != "") etRetExpFarmer.text.toString()
                    .toInt() else 0
            datum.expectedPda =
                if (etRetExpPDA.text.toString() != "") etRetExpPDA.text.toString().toInt() else 0
            datum.spName1 = etRetParName.text.toString().trim()
            datum.spNumber1 = etRetParNum.text.toString().trim()
            datum.spName2 = etRetParName2.text.toString().trim()
            datum.spNumber2 = etRetParNum2.text.toString().trim()
            datum.spName3 = etRetParName3.text.toString().trim()
            datum.spNumber3 = etRetParNum3.text.toString().trim()
            datum.spName4 = etRetParName4.text.toString().trim()
            datum.spNumber4 = etRetParNum4.text.toString().trim()
            datum.spName5 = etRetParName5.text.toString().trim()
            datum.spNumber5 = etRetParNum5.text.toString().trim()
            datum.spName6 = etRetParName6.text.toString().trim()
            datum.spNumber6 = etRetParNum6.text.toString().trim()
            datum.language = spinnerLanguage.text.toString()
            datum.language_id = languageId.toString()
            datum.userType = "bdo"
            datum.ddName = ddName
            datum.user_farmer = demoId.toString()
            datum.ddId = if (userType == "dhanuka_doctor") "0" else ddId
            datum.uploadedFile = ""
            datum.ticketStatus = "ACTIVE"
            datum.uploadedByType = ""
            val id = appDatabase.ticketHistoryDao().insert(datum)
            appDatabase.ticketHistoryDao().updateTicketId(id.toInt(), id.toInt())
            runOnUiThread {
                showdialog(ticketId)
                logEventForCreateTicket("Offline data", Gson().toJson(datum), false)
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
        }.subscribe()
    }

    private fun getCurrentDate(formatDate: String): String {
        val calendar = Calendar.getInstance(Locale.ENGLISH)
        val format = SimpleDateFormat(formatDate, Locale.ENGLISH)
        return format.format(calendar.timeInMillis)
    }

    private fun randomInt(digits: Int): Int {
        val minimum = Math.pow(10.0, digits - 1.toDouble()).toInt()
        val maximum = Math.pow(10.0, digits.toDouble()).toInt() - 1
        val random = Random()
        return minimum + random.nextInt(maximum - minimum + 1)
    }
}
