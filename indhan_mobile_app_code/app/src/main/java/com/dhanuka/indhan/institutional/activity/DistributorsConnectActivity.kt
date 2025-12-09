package com.dhanuka.indhan.institutional.activity

import android.Manifest
import android.app.Dialog
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.location.Location
import android.location.LocationManager
import android.os.Bundle
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.view.View
import android.view.Window
import android.widget.*
import androidx.core.app.ActivityCompat
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.institutional.adapter.DistributorListAdapter
import com.dhanuka.indhan.institutional.model.DistributorListData
import com.dhanuka.indhan.institutional.model.DistributorListResponse
import com.dhanuka.indhan.institutional.model.MarkArrivalDepartureDistributor
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.*
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import io.nlopez.smartlocation.SmartLocation
import kotlinx.android.synthetic.main.activity_distributor_listing.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import pl.aprilapps.easyphotopicker.DefaultCallback
import pl.aprilapps.easyphotopicker.EasyImage
import pl.aprilapps.easyphotopicker.MediaFile
import pl.aprilapps.easyphotopicker.MediaSource
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File
import java.text.SimpleDateFormat
import java.util.*

class DistributorsConnectActivity : BaseActivity(), SwipeRefreshLayout.OnRefreshListener,
    DistributorListAdapter.OnClickViewDistributorItem {

    private var selectTypeDistributor: NumberSpinner? = null
    private var appDataBase: AppDatabase? = null
    private var preference: Preference? = null
    private var distributorList = ArrayList<DistributorListData>()
    private var distributorListAll = ArrayList<DistributorListData>()
    private var distributorType = ArrayList<String>()
    private var searchStr: String = ""
    private var distributorAdapter: DistributorListAdapter? = null
    private var latitude: Double? = null
    private var longtitude: Double? = null
    private var userLocation: Location? = null
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    private var easyImage: EasyImage? = null
    private var file: File? = null

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.ivAddNewDistributor -> {
                launchIntent(AddNewDistributorOrAdvisorActivity::class.java, false)
            }

            R.id.ivBack -> {
                finish()
            }
            R.id.txtFilterTeaEstate -> {
                selectTypeDistributor?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.btnReset -> {
                getLocalData("")
                searchStr = ""
                txtFilterTeaEstate.text = ""
            }

            R.id.swipeRefreshLayout -> swipeRefreshLayout.post {
                swipeRefreshLayout.isRefreshing = true
            }

            R.id.layout_btn -> {
                launchIntent(OfflineArrivalDepartureDistributor::class.java, false)
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_distributor_listing)
        preference = Preference(this)
        appDataBase = DatabaseClient.getInstance(this).appDatabase
        fusedLocationClient =
            LocationServices.getFusedLocationProviderClient(this@DistributorsConnectActivity)
//        if (preference?.getString("user_type")?.contains("area") == true) {
//            ivAddNewDistributor.visibility = View.VISIBLE
//        } else {
//            ivAddNewDistributor.visibility = View.GONE
//        }
        initCamera()
        getLocation()
        setListeners()
        setDropDownData()
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getDistributorsList(false)
        } else {
            getLocalData("")
        }
    }

    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@DistributorsConnectActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    private fun getLocation() {
        checkPermission(
            arrayOf(Manifest.permission.ACCESS_FINE_LOCATION),
            object : PermissionListener {
                override fun onPermissionStatus(status: Int) {
                    if (status == com.dhanuka.indhan.Activity.Constants.PERMISSION_GRANTED)
                        onLocationPermissionGranted()
                    else if (status == com.dhanuka.indhan.Activity.Constants.PERMISSION_SET_NEVER_AGAIN)
                        showPermissionSettingDialog()
                }
            })
    }

    private fun setDropDownData() {
        distributorType.clear()
        distributorType.add("Distributor")
        distributorType.add("Advisor")

        selectTypeDistributor =
            NumberSpinner(this, distributorType, "Search or Select Distributor Type")
        selectTypeDistributor?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (item == "Distributor") {
                    getLocalData("0")
                } else {
                    getLocalData("1")
                }
                txtFilterTeaEstate.text = item
            }
        })
    }

    private fun getLocalData(s: String) {
        distributorList.clear()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                distributorList = if (TextUtils.isEmpty(s)) {
                    (appDataBase?.distributorListDataDao()?.allDistributors()
                        ?: ArrayList<DistributorListData>()) as ArrayList<DistributorListData>
                } else {
                    (appDataBase?.distributorListDataDao()?.allDistributorsByType(s)
                        ?: ArrayList<DistributorListData>()) as ArrayList<DistributorListData>
                }
                distributorListAll = ArrayList<DistributorListData>(distributorList)
            }.run {
                withContext(Dispatchers.Main) {
                    setAdapter()
                }
            }
        }
    }

    private fun setAdapter() {
        distributorAdapter =
            DistributorListAdapter(this@DistributorsConnectActivity, distributorList)
        rcvDistributorList.adapter = distributorAdapter
        distributorAdapter?.setViewClickListener(this)
        if (distributorList.size > 0) {
            tv_nodata.visibility = View.GONE
            rcvDistributorList.visibility = View.VISIBLE
        } else {
            tv_nodata.visibility = View.VISIBLE
            rcvDistributorList.visibility = View.GONE
        }
    }

    private fun getDistributorsList(b: Boolean) {
        swipeRefreshLayout.isRefreshing = false
        showProgress()
        val token = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = "v3/getDistributorAdvisorList".getLastHitTimeData()
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_date"] = if (b) "0" else time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).getDistributorData(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<DistributorListResponse>() {
                            override fun onCompleted() {
                                hideProgress()
                            }

                            override fun onError(e: Throwable?) {
                                hideProgress()
                            }

                            override fun onNext(it: DistributorListResponse?) {
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertDataInDb(it.data)
                                    } else if (it.status_code == 409) {
                                        EventBus.getDefault().post("logout")
                                    }
                                }
                            }
                        })
                }
            }
        }
    }

    private fun insertDataInDb(data: List<DistributorListData>) {
        var allArrivalDepartureData = ArrayList<MarkArrivalDepartureDistributor>()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                allArrivalDepartureData =
                    (appDataBase?.arrivalDepartureDistributorDao()?.getAllArrivalDepartureListData()
                        ?: ArrayList<MarkArrivalDepartureDistributor>()) as ArrayList<MarkArrivalDepartureDistributor>
            }.run {
                withContext(Dispatchers.Main) {
                    data.let {
                        if (allArrivalDepartureData.isNotEmpty()) {
                            for ((index) in it.withIndex()) {
                                it[index].is_visited = 0
                                for ((indexWhere) in allArrivalDepartureData.withIndex()) {
                                    if (it[index].distributor_advisor_id == allArrivalDepartureData[indexWhere].distributor_id) {
                                        if (allArrivalDepartureData[indexWhere].departure_date == null) {
                                            it[index].is_visited = 1
                                            break
                                        } else {
                                            it[index].is_visited = 2
                                            break
                                        }
                                    }
                                }
                            }
                        } else {
                            for ((index) in it.withIndex()) {
                                it[index].is_visited = 0
                            }
                        }
                    }
                }.run {
                    withContext(Dispatchers.IO) {
                        data.forEach() {
                            appDataBase?.distributorListDataDao()?.insert(it)
                        }
                    }.run {
                        withContext(Dispatchers.Main) {
                            "v3/getDistributorAdvisorList".updateTimeOnDb()
                            getLocalData("")
                        }
                    }
                }
            }
        }
    }

    private fun setListeners() {
        ivAddNewDistributor.setOnClickListener(this)
        txtFilterTeaEstate.setOnClickListener(this)
        layout_btn.setOnClickListener(this)
        swipeRefreshLayout.setOnRefreshListener(this)
        btnReset.setOnClickListener(this)
        ivBack.setOnClickListener(this)

        searchBox.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {

            }

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
            }

            override fun afterTextChanged(s: Editable?) {
                if (!TextUtils.isEmpty(s.toString())) {
                    distributorList.clear()
                    distributorListAll.forEach() { distributor ->
                        distributor.let {
                            if ((it.distributor_firm_name ?: "").lowercase()
                                    .contains(s.toString().lowercase())
                                || (it.distributor_name ?: "").lowercase()
                                    .contains(s.toString().lowercase())
                                || ((it.brands ?: "").lowercase()
                                    .contains(s.toString().lowercase()))
                                || (it.crope ?: "").lowercase().contains(s.toString().lowercase())
                                || ((it.distributor_type ?: "").lowercase()
                                    .contains(s.toString().lowercase()))
                                || ((it.market ?: "").lowercase()
                                    .contains(s.toString().lowercase()))
                                || ((it.territory ?: "").lowercase()
                                    .contains(s.toString().lowercase()))
                                || ((it.address ?: "").lowercase()
                                    .contains(s.toString().lowercase()))
                                || ((it.district ?: "").lowercase()
                                    .contains(s.toString().lowercase()))
                                || ((it.state ?: "").lowercase().contains(s.toString().lowercase()))
                            ) {
                                distributorList.add(it)
                            }
                        }
                    }
                    GlobalScope.launch {
                        delay(300)
                        withContext(Dispatchers.Main) {
                            if (distributorList.size > 0) {

                            } else {
                                makeToast("No matched data found.")
                            }
                            setAdapter()
                        }
                    }
                } else {
                    distributorList.clear()
                    distributorList.addAll(distributorListAll)
                    setAdapter()
                }
            }

        })
    }

    private fun String.getLastHitTimeData(): ApiTimeTableModel? {
        return appDataBase?.apiTimeDao()?.getApiTimeData(this)
    }

    private fun String.updateTimeOnDb() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val timeData = this@updateTimeOnDb.getLastHitTimeData()
                val sdf = SimpleDateFormat(Constants.DATE_TIME_FORMAT)
                val currentDate = sdf.format(Date())
                if (timeData != null) {
                    appDataBase?.apiTimeDao()?.updateTimeInDb(this@updateTimeOnDb, currentDate)
                } else {
                    val timeDataModel = ApiTimeTableModel()
                    timeDataModel.apiName = this@updateTimeOnDb
                    timeDataModel.updatedAt = currentDate
                    appDataBase?.apiTimeDao()?.insert(timeDataModel)
                }
            }
        }
    }

    override fun onRefresh() {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            swipeRefreshLayout.isRefreshing = true
            getDistributorsList(true)
            searchStr = ""
            searchBox.setText(searchStr)
            txtFilterTeaEstate.text = ""
        }
    }

    override fun onClickViewVisit(view: View, subMenu: DistributorListData) {
        val bundle = Bundle()
        bundle.putInt("id", subMenu.distributor_advisor_id)
        launchIntent(ViewVisitHistoryActivity::class.java, bundle, false)
    }

    override fun onClickArrivalView(pos: Int, subMenu: DistributorListData) {
        showDialog(pos, 1, subMenu)
    }

    override fun onClickDepartureView(pos: Int, subMenu: DistributorListData) {
        showDialog(pos, 2, subMenu)
    }

    private fun showDialog(position: Int, isArrivalDeparture: Int, subMenu: DistributorListData) {
        if (latitude == null || longtitude == null) {
            getLocation()
            return
        }

        val dialog = Dialog(this@DistributorsConnectActivity)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_add_feedback)
        dialog.setCanceledOnTouchOutside(true)
        dialog.setCancelable(true)
        val msg = dialog.findViewById<View>(R.id.edtFeedback) as EditText
        val tvOkay = dialog.findViewById<View>(R.id.btnSubmit) as Button
        val captureButton = dialog.findViewById<View>(R.id.txtCaptureImage) as TextView
        val imgView = dialog.findViewById<View>(R.id.imgView) as ImageView

        val ticketData = distributorList[position]

        imgView.setOnClickListener {
            PermissionsUtil.askPermissions(
                this@DistributorsConnectActivity,
                PermissionsUtil.CAMERA,
                PermissionsUtil.STORAGE
            ) {
                if (it) {
                    ImagePickerUtil.pickFromCamera2(
                        easyImage,
                        this@DistributorsConnectActivity,
                        { imageFile, _ ->
                            file = ImageUtils.compressToExistingFile(
                                imageFile.absolutePath,
                                imageFile
                            )
                            if (file == null) {
                                file = imageFile
                            }
                            imgView.setImageBitmap(FileUtility.createBitmapFromFile(file))
                        },
                        "ImageData2"
                    )
                }
            }
        }

        captureButton.setOnClickListener {
            PermissionsUtil.askPermissions(
                this@DistributorsConnectActivity,
                PermissionsUtil.CAMERA,
                PermissionsUtil.STORAGE
            ) {
                if (it) {
                    ImagePickerUtil.pickFromCamera2(
                        easyImage,
                        this@DistributorsConnectActivity,
                        { imageFile, _ ->
                            file = ImageUtils.compressToExistingFile(
                                imageFile.absolutePath,
                                imageFile
                            )
                            if (file == null) {
                                file = imageFile
                            }
                            imgView.setImageBitmap(FileUtility.createBitmapFromFile(file))
                        },
                        "ImageData"
                    )
                }
            }
        }

        tvOkay.setOnClickListener {
            if (latitude == null || longtitude == null) {
                getLocation()
                Toast.makeText(
                    this@DistributorsConnectActivity,
                    "Unable to fetch Current Location. Try Again!!",
                    Toast.LENGTH_LONG
                ).show()
            } else {
                val sdfFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
                val dateFormat = sdfFormat.format(Calendar.getInstance(Locale.ENGLISH).timeInMillis)
                var model = MarkArrivalDepartureDistributor()
                if (file != null) {
                    try {
                        GlobalScope.launch {
                            withContext(Dispatchers.IO) {
                                model = appDataBase?.arrivalDepartureDistributorDao()
                                    ?.getAllArrivalDepartureById(ticketData.distributor_advisor_id)
                                    ?: MarkArrivalDepartureDistributor()
                            }.run {
                                withContext(Dispatchers.Main) {
                                    if (isArrivalDeparture == 1) {
                                        model.distributor_id = ticketData.distributor_advisor_id
                                        model.arrival_comment = msg.text.toString()
                                        model.arrival_image = file!!.absolutePath
                                        model.arrival_lat = latitude.toString()
                                        model.arrival_lng = longtitude.toString()
                                        model.uploaded_server_status = 0
                                        model.arrival_departure_status = 1
                                        distributorList[position].is_visited = 1
                                        model.arrival_date = dateFormat.toString()
                                        val currentHitTimeTwoHour = System.currentTimeMillis()
                                        model.server_date_time = currentHitTimeTwoHour
                                    } else {
                                        model.departure_comment = msg.text.toString()
                                        model.departure_image = file!!.absolutePath
                                        model.departure_lat = latitude.toString()
                                        model.departure_lng = longtitude.toString()
                                        model.uploaded_server_status = 0
                                        model.arrival_departure_status = 2
                                        distributorList[position].is_visited = 2
                                        model.departure_date = dateFormat.toString()
                                        val diff =
                                            Utility.findDifference(
                                                sdfFormat,
                                                model.arrival_date,
                                                model.departure_date
                                            )
                                        model.duration = "$diff"
                                    }
                                }.run {
                                    withContext(Dispatchers.IO) {
                                        appDataBase?.distributorListDataDao()
                                            ?.insert(distributorList[position])
                                        if (isArrivalDeparture == 2) {
                                            appDataBase
                                                ?.arrivalDepartureDistributorDao()
                                                ?.updateDepartureData(
                                                    model.arrival_departure_status!!,
                                                    model.distributor_id,
                                                    model.departure_comment!!,
                                                    model.departure_date!!,
                                                    model.departure_image!!,
                                                    model.departure_lat!!,
                                                    model.departure_lng!!,
                                                    model.uploaded_server_status!!,
                                                    model.duration,
                                                    model.server_date_time!!
                                                )
                                        } else {
                                            appDataBase
                                                ?.arrivalDepartureDistributorDao()
                                                ?.insert(model)
                                        }
                                    }.run {
                                        withContext(Dispatchers.Main) {
                                            if (distributorAdapter != null) {
                                                distributorAdapter?.notifyDataSetChanged()
                                                if (CheckConnectionStatus.isInternetAvailable(this@DistributorsConnectActivity)) {
                                                    OfflineDatabase(this@DistributorsConnectActivity).uploadArrivalDepartureDistributorData()
                                                }
                                                if (isArrivalDeparture == 2) {
                                                    makeToast("Your departure data has been saved.")
                                                } else {
                                                    makeToast("Your arrival data has been saved.")
                                                }
                                            }
                                            dialog.dismiss()
                                        }
                                    }
                                }
                            }
                        }
                    } catch (e: Exception) {
                        e.printStackTrace()
                    }
                } else {
                    Toast.makeText(
                        this@DistributorsConnectActivity,
                        "Please select image to proceed.",
                        Toast.LENGTH_LONG
                    ).show()
                }
            }
        }

        dialog.show()
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        easyImage?.handleActivityResult(
            requestCode,
            resultCode,
            data,
            this@DistributorsConnectActivity,
            object : DefaultCallback() {
                override fun onMediaFilesPicked(imageFiles: Array<MediaFile>, source: MediaSource) {
                    onPhotosReturned(imageFiles);
                }
            })
    }

    private fun onPhotosReturned(imageFiles: Array<MediaFile>) {
        if (imageFiles.isNotEmpty()) {
            ImagePickerUtil.onActivityResult2(10000, BaseActivity.RESULT_OK, imageFiles[0])
        }
    }

    private fun onLocationPermissionGranted() {
        if (ActivityCompat.checkSelfPermission(
                this@DistributorsConnectActivity,
                Manifest.permission.ACCESS_FINE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(
                this@DistributorsConnectActivity,
                Manifest.permission.ACCESS_COARSE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            return
        }

        val loc =
            this@DistributorsConnectActivity.getSystemService(Context.LOCATION_SERVICE) as LocationManager
        if (!loc.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
            Toast.makeText(
                this@DistributorsConnectActivity,
                "Please Enable GPS.",
                Toast.LENGTH_LONG
            )
                .show()
            return
        }
        fusedLocationClient.lastLocation.addOnSuccessListener(this@DistributorsConnectActivity) { location ->
            if (location != null) {
                userLocation = location
                latitude = userLocation?.latitude
                longtitude = userLocation?.longitude
            } else {
                userLocation = null
                SmartLocation.with(this@DistributorsConnectActivity).location().start {
                    if (it != null) {
                        latitude = it.latitude
                        longtitude = it.longitude
                    }
                }
                Toast.makeText(
                    this@DistributorsConnectActivity,
                    "Not Able to Fetch Current Location. Try Again!!",
                    Toast.LENGTH_LONG
                ).show()
            }
        }
    }

}