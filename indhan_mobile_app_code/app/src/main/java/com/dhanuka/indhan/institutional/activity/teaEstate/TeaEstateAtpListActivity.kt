package com.dhanuka.indhan.institutional.activity.teaEstate

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
import android.text.TextUtils
import android.view.View
import android.view.Window
import android.widget.*
import androidx.core.app.ActivityCompat
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.institutional.adapter.teaEstate.TeaEstatePlanAtpListAdapter
import com.dhanuka.indhan.institutional.model.RefreshEventModel
import com.dhanuka.indhan.institutional.model.teaEstate.AtpListData
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateAtpListResponse
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateMarkArrivalDepartureAtpModel
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.*
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import com.google.gson.Gson
import io.nlopez.smartlocation.SmartLocation
import kotlinx.android.synthetic.main.activity_plan_atp_tea_estate.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import org.greenrobot.eventbus.Subscribe
import org.greenrobot.eventbus.ThreadMode
import org.joda.time.DateTime
import org.joda.time.format.DateTimeFormat
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

class TeaEstateAtpListActivity : BaseActivity(), TeaEstatePlanAtpListAdapter.OnClickItem,
    SwipeRefreshLayout.OnRefreshListener {

    private var userEcn: String = ""
    private var userType: String = ""
    private var planAtpListAdapter: TeaEstatePlanAtpListAdapter? = null
    private var planAtpList: ArrayList<AtpListData> = ArrayList()
    private var planAtpListCopy: ArrayList<AtpListData> = ArrayList()
    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    private var latitude: Double? = null
    private var longtitude: Double? = null
    private var userLocation: Location? = null
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    private var easyImage: EasyImage? = null
    private var file: File? = null
    private var parentTeaId: String = ""
    private var teaType: String = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_plan_atp_tea_estate)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        fusedLocationClient =
            LocationServices.getFusedLocationProviderClient(this@TeaEstateAtpListActivity)
        initCamera()
        getLocation()
        setListeners()
        setAdapter()
        userType = preference?.getString("user_type") ?: ""
        userEcn = preference?.getString("ecn") ?: ""
        intent?.getStringExtra("for")?.let {
            parentTeaId = intent?.getStringExtra("parentId") ?: ""
            teaType = intent?.getStringExtra("type") ?: ""
        }

        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getPlanAtpData(false)
        } else {
            getLocalData()
        }
    }

    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@TeaEstateAtpListActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    private fun getPlanAtpData(b: Boolean) {
        swipeRefreshLayout.isRefreshing = false
        showProgress()
        val token = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = "v3/teaEstateAtpListing".getLastHitTimeData()
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_date"] = if (b) "0" else time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).getTeaAtpListing(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<TeaEstateAtpListResponse>() {
                            override fun onCompleted() {
                                hideProgress()
                            }

                            override fun onError(e: Throwable?) {
                                hideProgress()
                            }

                            override fun onNext(it: TeaEstateAtpListResponse?) {
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

    private fun insertDataInDb(data: List<AtpListData>?) {
        var allArrivalDepartureData = ArrayList<TeaEstateMarkArrivalDepartureAtpModel>()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                allArrivalDepartureData =
                    (appDatabase?.markArrivalDepartureAtpDao()?.getAllArrivalDepartureListData()
                        ?: ArrayList<TeaEstateMarkArrivalDepartureAtpModel>()) as ArrayList<TeaEstateMarkArrivalDepartureAtpModel>
            }.run {
                withContext(Dispatchers.Main) {
                    data?.let {
                        if (!allArrivalDepartureData.isNullOrEmpty()) {
                            for ((index) in it.withIndex()) {
                                it[index].is_arrival_departure = 0
                                val ticketDate = it[index].date_time!!
                                var sdf =
                                    SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSZZZ", Locale.ENGLISH)
                                val strDate: Date = sdf.parse(ticketDate)
                                sdf = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
                                val date = sdf.format(strDate)
                                val currentDate =
                                    sdf.format(Calendar.getInstance(Locale.ENGLISH).time)
                                var value = 0
                                if (currentDate != date) {
                                    value = 1
                                    it[index].is_arrival_departure = 1
                                }
                                if (TextUtils.isEmpty(it[index].departure_image) && value == 0) {
                                    it[index].is_arrival_departure = 0
                                }
                                if (currentDate > date) {
                                    it[index].is_arrival_departure = 2
                                }
                                if (it[index].is_arrival_departure == 1) {
                                    it[index].is_arrival_departure = 2
                                }
                                if (!TextUtils.isEmpty(it[index].departure_image) && !TextUtils.isEmpty(
                                        it[index].arrival_image
                                    )
                                ) {
                                    it[index].is_arrival_departure = 2
                                }

                                for ((indexWhere) in allArrivalDepartureData.withIndex()) {
                                    if (it[index].estate_atp_id == allArrivalDepartureData[indexWhere].atp_id) {
                                        if (allArrivalDepartureData[indexWhere].departure_date == null) {
                                            it[index].is_arrival_departure = 1
                                        } else {
                                            it[index].is_arrival_departure = 2
                                        }
                                    }
                                }
                            }
                        } else {
                            for ((index) in it.withIndex()) {
                                val ticketDate = it[index].date_time!!
                                var sdf =
                                    SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSZZZ", Locale.ENGLISH)
                                val strDate: Date = sdf.parse(ticketDate)
                                sdf = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
                                val date = sdf.format(strDate)
                                val currentDate =
                                    sdf.format(Calendar.getInstance(Locale.ENGLISH).time)
                                var value = 0
                                if (currentDate != date) {
                                    value = 1
                                    it[index].is_arrival_departure = 1
                                }
                                if (TextUtils.isEmpty(it[index].departure_image) && value == 0) {
                                    it[index].is_arrival_departure = 0
                                }
                                if (currentDate > date) {
                                    it[index].is_arrival_departure = 2
                                }
                                if (it[index].is_arrival_departure == 1) {
                                    it[index].is_arrival_departure = 2
                                }
                                if (!TextUtils.isEmpty(it[index].departure_image) && !TextUtils.isEmpty(
                                        it[index].arrival_image
                                    )
                                ) {
                                    it[index].is_arrival_departure = 2
                                }
                            }
                        }
                    }
                    hideProgress()
                }.run {
                    withContext(Dispatchers.IO) {
                        data.let {
                            it?.forEach() { teaEstate ->
                                appDatabase?.atpListDataDao()?.insert(teaEstate)
                            }
                        }
                        "v3/teaEstateAtpListing".updateTimeOnDb()
                        delay(300)
                        getLocalData()
                    }
                }
            }
        }
    }

    private fun getLocalData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                planAtpList = if (!TextUtils.isEmpty(parentTeaId)) {
                    (appDatabase?.atpListDataDao()?.getAllById(parentTeaId.toInt())
                        ?: ArrayList<AtpListData>()) as ArrayList<AtpListData>
                } else {
                    (appDatabase?.atpListDataDao()?.getAllAtpListData()
                        ?: ArrayList<AtpListData>()) as ArrayList<AtpListData>
                }
            }.run {
                withContext(Dispatchers.Main) {
                    setAdapter()
                }
            }
        }
    }

    private fun String.getLastHitTimeData(): ApiTimeTableModel? {
        return appDatabase?.apiTimeDao()?.getApiTimeData(this)
    }

    private fun String.updateTimeOnDb() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val timeData = this@updateTimeOnDb.getLastHitTimeData()
                val sdf = SimpleDateFormat(Constants.DATE_TIME_FORMAT)
                val currentDate = sdf.format(Date())
                if (timeData != null) {
                    appDatabase?.apiTimeDao()?.updateTimeInDb(this@updateTimeOnDb, currentDate)
                } else {
                    val timeDataModel = ApiTimeTableModel()
                    timeDataModel.apiName = this@updateTimeOnDb
                    timeDataModel.updatedAt = currentDate
                    appDatabase?.apiTimeDao()?.insert(timeDataModel)
                }
            }
        }
    }

    private fun setAdapter() {
        planAtpList.forEach() {
            if (it.manager_ecn.equals(userEcn, ignoreCase = true)) {
                it.typeOfData = "planned"
            }
            if (it.departure_image != null) {
                it.typeOfData = "visited"
            }
        }

        planAtpList.forEach() {
            val ticketDate = it.date_time!!
            var sdf =
                SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSZZZ", Locale.ENGLISH)
            val strDate: Date? = sdf.parse(ticketDate)
            sdf = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
            val date = sdf.format(strDate!!)
            val currentDate =
                sdf.format(Calendar.getInstance(Locale.ENGLISH).time)
            if ((currentDate > date) && ((userEcn.equals(
                    it.manager_ecn,
                    ignoreCase = true
                )))
            ) {
                if (it.typeOfData != null) {
                    if (!it.typeOfData.equals("visited")) {
                        it.typeOfData = "expired"
                    }
                }
            }
        }

        planAtpListCopy = ArrayList(planAtpList)
        planAtpList.clear()
        val formatter = DateTimeFormat.forPattern("yyyy-MM-dd")
        val currentDate = formatter.parseDateTime(DateTime.now().toString(formatter))
        planAtpListCopy.forEach() {
            val ticketDate = formatter.parseDateTime(it.date_time?.split("T")?.get(0)!!)
            if (ticketDate == currentDate) {
                planAtpList.add(it)
            }
        }
       val planAtpListCopyNew: ArrayList<AtpListData> = ArrayList()
        planAtpListCopy.forEach() {
            val ticketDate = formatter.parseDateTime(it.date_time?.split("T")?.get(0)!!)
            if (ticketDate != currentDate) {
                planAtpListCopyNew.add(it)
                planAtpListCopyNew.sortByDescending { it1-> Utility.getDateTimeStr(it1.date_time!!).time }
            }
        }
        planAtpList.addAll(planAtpListCopyNew)
        planAtpListAdapter = TeaEstatePlanAtpListAdapter(this@TeaEstateAtpListActivity, planAtpList)
        recyclerAtpList.adapter = planAtpListAdapter
        planAtpListAdapter?.setClickListener(this)
        if (planAtpList.size > 0) {
            txtNoTickets.visibility = View.GONE
            recyclerAtpList.visibility = View.VISIBLE
        } else {
            txtNoTickets.visibility = View.VISIBLE
            recyclerAtpList.visibility = View.GONE
        }
    }

    private fun setListeners() {
        ivPlanAtpTeaEstate.setOnClickListener(this)
        swipeRefreshLayout.setOnRefreshListener(this)
        ivBack.setOnClickListener(this)
        layout_btn.setOnClickListener(this)
        chbAll.setOnCheckedChangeListener { buttonView, isChecked ->
            if (isChecked) {
                chbAll?.isChecked = true
                chbPlanned?.isChecked = false
                chbVisited?.isChecked = false
                chbExpired?.isChecked = false
                filterList("")
            } else {
                chbAll?.isChecked = false
            }
        }
        chbPlanned.setOnCheckedChangeListener { buttonView, isChecked ->
            if (isChecked) {
                chbAll?.isChecked = false
                chbPlanned?.isChecked = true
                chbVisited?.isChecked = false
                chbExpired?.isChecked = false
                filterList("planned")
            } else {
                chbPlanned?.isChecked = false
            }
        }
        chbVisited.setOnCheckedChangeListener { buttonView, isChecked ->
            if (isChecked) {
                chbAll?.isChecked = false
                chbPlanned?.isChecked = false
                chbVisited?.isChecked = true
                chbExpired?.isChecked = false
                filterList("visited")
            } else {
                chbVisited?.isChecked = false
            }
        }
        chbExpired.setOnCheckedChangeListener { buttonView, isChecked ->
            if (isChecked) {
                chbAll?.isChecked = false
                chbPlanned?.isChecked = false
                chbVisited?.isChecked = false
                chbExpired?.isChecked = true
                filterList("expired")
            } else {
                chbExpired?.isChecked = false
            }
        }
    }

    private fun filterList(s: String) {
        when (s) {
            "planned" -> {
                planAtpList.clear()
                planAtpListCopy.forEach() {
                    if (it.typeOfData.equals("planned", ignoreCase = true)) {
                        planAtpList.add(it)
                    }
                }
                planAtpListAdapter?.notifyDataSetChanged()
            }
            "visited" -> {
                planAtpList.clear()
                planAtpListCopy.forEach() {
                    if (it.typeOfData.equals("visited", ignoreCase = true)) {
                        planAtpList.add(it)
                    }
                }
                planAtpListAdapter?.notifyDataSetChanged()
            }
            "expired" -> {
                planAtpList.clear()
                planAtpListCopy.forEach() {
                    if (it.typeOfData.equals("expired", ignoreCase = true)) {
                        planAtpList.add(it)
                    }
                }
                planAtpListAdapter?.notifyDataSetChanged()
            }
            else -> {
                planAtpList.clear()
                getLocalData()
            }
        }
    }

    override fun onClickView(view: View, subMenu: AtpListData) {
        val bundle = Bundle()
        bundle.putString("atp_data", Gson().toJson(subMenu))
        launchIntent(TeaEstateAtpDetailActivity::class.java, bundle, false)
    }

    override fun onArrivalClickView(position: Int, subMenu: AtpListData) {
        //Arrival
        showDialog(position, 1, subMenu)
    }

    override fun onDepartureClickView(position: Int, subMenu: AtpListData) {
        //Departure
        showDialog(position, 2, subMenu)
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

    private fun showDialog(position: Int, isArrivalDeparture: Int, subMenu: AtpListData) {
        if (latitude == null || longtitude == null) {
            getLocation()
            return
        }

        val ticketDate = planAtpList[position].date_time
        var sdf = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSZZZ", Locale.ENGLISH)
        val strDate: Date = sdf.parse(ticketDate)!!
        sdf = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
        val date = sdf.format(strDate)
        val currentDate = sdf.format(Calendar.getInstance(Locale.ENGLISH).time)
        if (currentDate != date) {
            Toast.makeText(
                this@TeaEstateAtpListActivity,
                "This Ticket is not for today",
                Toast.LENGTH_LONG
            )
                .show()
            return
        }
        val dialog = Dialog(this@TeaEstateAtpListActivity)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_add_feedback)
        dialog.setCanceledOnTouchOutside(true)
        dialog.setCancelable(true)
        val msg = dialog.findViewById<View>(R.id.edtFeedback) as EditText
        val tvOkay = dialog.findViewById<View>(R.id.btnSubmit) as Button
        val captureButton = dialog.findViewById<View>(R.id.txtCaptureImage) as TextView
        val imgView = dialog.findViewById<View>(R.id.imgView) as ImageView

        val ticketData = planAtpList[position]

        imgView.setOnClickListener {
            PermissionsUtil.askPermissions(
                this@TeaEstateAtpListActivity,
                PermissionsUtil.CAMERA,
                PermissionsUtil.STORAGE
            ) {
                if (it) {
                    ImagePickerUtil.pickFromCamera2(
                        easyImage,
                        this@TeaEstateAtpListActivity,
                        { imageFile, _ ->
                            file = ImageUtils.compressToExistingFile(
                                imageFile.absolutePath,
                                imageFile
                            )
                            if(file == null){
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
                this@TeaEstateAtpListActivity,
                PermissionsUtil.CAMERA,
                PermissionsUtil.STORAGE
            ) {
                if (it) {
                    ImagePickerUtil.pickFromCamera2(
                        easyImage,
                        this@TeaEstateAtpListActivity,
                        { imageFile, _ ->
                            file = ImageUtils.compressToExistingFile(
                                imageFile.absolutePath,
                                imageFile
                            )
                            if(file == null){
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
                    this@TeaEstateAtpListActivity,
                    "Unable to fetch Current Location. Try Again!!",
                    Toast.LENGTH_LONG
                ).show()
            } else {
                val sdfFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
                val dateFormat = sdfFormat.format(Calendar.getInstance(Locale.ENGLISH).timeInMillis)
                var model = TeaEstateMarkArrivalDepartureAtpModel()
                if (file != null) {
                    try {
                        GlobalScope.launch {
                            withContext(Dispatchers.IO) {
                                model = appDatabase?.markArrivalDepartureAtpDao()
                                    ?.getAllArrivalDepartureById(ticketData.estate_atp_id)
                                    ?: TeaEstateMarkArrivalDepartureAtpModel()
                            }.run {
                                withContext(Dispatchers.Main) {
                                    if (isArrivalDeparture == 1) {
                                        model.atp_id = ticketData.estate_atp_id
                                        model.tea_estate_id = ticketData.tea_estate_id
                                        model.ticket_number = ticketData.ticket_number
                                        model.arrival_comment = msg.text.toString()
                                        model.arrival_image = file!!.absolutePath
                                        model.arrival_lat = latitude.toString()
                                        model.arrival_lng = longtitude.toString()
                                        model.uploaded_server_status = 0
                                        model.arrival_departure_status = 1
                                        planAtpList[position].is_arrival_departure = 1
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
                                        planAtpList[position].is_arrival_departure = 2
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
                                        appDatabase?.atpListDataDao()?.insert(planAtpList[position])
                                        if (isArrivalDeparture == 2) {
                                            appDatabase
                                                ?.markArrivalDepartureAtpDao()
                                                ?.updateDepartureData(
                                                    model.arrival_departure_status!!,
                                                    model.atp_id,
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
                                            appDatabase
                                                ?.markArrivalDepartureAtpDao()
                                                ?.insert(model)
                                        }
                                    }.run {
                                        withContext(Dispatchers.Main) {
                                            if (planAtpListAdapter != null) {
                                                planAtpListAdapter?.notifyDataSetChanged()
                                                if (CheckConnectionStatus.isInternetAvailable(this@TeaEstateAtpListActivity)) {
                                                    OfflineDatabase(this@TeaEstateAtpListActivity).uploadArrivalDepartureAtpTeaEstateData()
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
                        this@TeaEstateAtpListActivity,
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
            this@TeaEstateAtpListActivity,
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
                this@TeaEstateAtpListActivity,
                Manifest.permission.ACCESS_FINE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(
                this@TeaEstateAtpListActivity,
                Manifest.permission.ACCESS_COARSE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            return
        }

        val loc =
            this@TeaEstateAtpListActivity.getSystemService(Context.LOCATION_SERVICE) as LocationManager
        if (!loc.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
            Toast.makeText(this@TeaEstateAtpListActivity, "Please Enable GPS.", Toast.LENGTH_LONG)
                .show()
            return
        }
        fusedLocationClient.lastLocation.addOnSuccessListener(this@TeaEstateAtpListActivity) { location ->
            if (location != null) {
                userLocation = location
                latitude = userLocation?.latitude
                longtitude = userLocation?.longitude
            } else {
                userLocation = null
                SmartLocation.with(this@TeaEstateAtpListActivity).location().start {
                    if (it != null) {
                        latitude = it.latitude
                        longtitude = it.longitude
                    }
                }
                Toast.makeText(
                    this@TeaEstateAtpListActivity,
                    "Not Able to Fetch Current Location. Try Again!!",
                    Toast.LENGTH_LONG
                ).show()
            }
        }
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.ivPlanAtpTeaEstate -> {

            }
            R.id.ivBack -> {
                EventBus.getDefault().post(RefreshEventModel(data = 1, page = "tea_estate"))
                finish()
            }
            R.id.layout_btn -> {
                launchIntent(OfflineArrivalDepartureTeaEstate::class.java, false)
            }
            R.id.swipeRefreshLayout -> swipeRefreshLayout.post {
                swipeRefreshLayout.isRefreshing = true
            }
        }
    }

    override fun onRefresh() {
        swipeRefreshLayout.isRefreshing = true
        chbAll?.isChecked = true
        chbPlanned?.isChecked = false
        chbVisited?.isChecked = false
        chbExpired?.isChecked = false
        getPlanAtpData(true)
    }

    override fun onStart() {
        super.onStart()
        if (!EventBus.getDefault().isRegistered(this@TeaEstateAtpListActivity))
            EventBus.getDefault().register(this@TeaEstateAtpListActivity)
    }


    override fun onDestroy() {
        EventBus.getDefault().unregister(this@TeaEstateAtpListActivity)
        super.onDestroy()
    }

    @Subscribe(threadMode = ThreadMode.MAIN)
    public fun onMessageEvent(event: RefreshEventModel?) {
        if (event != null) {
            if (event.data == 2) {
                if (CheckConnectionStatus.isInternetAvailable(this@TeaEstateAtpListActivity)) {
                    getPlanAtpData(true)
                }
            }
        }
    }
}