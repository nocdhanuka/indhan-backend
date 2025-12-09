package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment

import android.Manifest
import android.app.Dialog
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.content.pm.PackageManager
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.location.Location
import android.location.LocationManager
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.view.Window
import android.widget.*
import androidx.core.app.ActivityCompat
import androidx.fragment.app.Fragment
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.SupervisorATPActivity
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.SupervisorActivateBean
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.SupervisorActivateTicketsAdapter
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.ParentActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DataBaseHelperNew
import com.dhanuka.indhan.DataBase.TableDataModel.ArrivalDepartureModelOffline
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.SelfParticipartionOffline.SelfParticipationOfflineActivity
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.*
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import com.google.gson.Gson
import io.nlopez.smartlocation.SmartLocation
import kotlinx.android.synthetic.main.fragment_active_ticket.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.greenrobot.eventbus.EventBus
import org.joda.time.DateTime
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
import kotlin.collections.set

/**
 * A simple [Fragment] subclass.
 */
class ActiveTicketFragment : BaseFragment() {
    private lateinit var appDatabase: AppDatabase
    private lateinit var mContext: SupervisorATPActivity
    private lateinit var preference: Preference
    private var file: File? = null
    private var instances: ActiveTicketFragment? = null
    private var productId = ""
    private var cropId = ""
    private var fromDate = ""
    private var endDate = ""
    private var rsmId = ""
    private var asmId = ""
    private var flId = ""
    private var ddId = ""
    private var adapter: SupervisorActivateTicketsAdapter? = null
    private val list = ArrayList<SupervisorActivateBean.Data>()
    private lateinit var dbManager: DataBaseHelperNew
    private var latitude: Double? = null
    private var longtitude: Double? = null
    private var userLocation: Location? = null
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    private var easyImage: EasyImage? = null

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        instances = this
        mContext = requireContext() as SupervisorATPActivity
        preference = Preference(mContext)
        initCamera()
        dbManager = DataBaseHelperNew(requireContext())
        appDatabase = DatabaseClient.getInstance(requireContext()).appDatabase
        return inflater.inflate(R.layout.fragment_active_ticket, container, false)
    }

    private fun initCamera() {
        easyImage =
            EasyImage.Builder(mContext)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    override fun onActivityCreated(savedInstanceState: Bundle?) {
        super.onActivityCreated(savedInstanceState)

        fusedLocationClient = LocationServices.getFusedLocationProviderClient(requireActivity())

        getLocation()

        LocalBroadcastManager.getInstance(mContext)
            .registerReceiver(mMessageReceiver, IntentFilter("custom-event-name"))
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["rsm"] = rsmId
        query["asm"] = asmId
        query["bdo_id"] = flId
        query["dd_id"] = ddId
        query["product"] = productId
        query["crop"] = cropId
        query["from_date"] = fromDate
        query["to_date"] = endDate
        if (CheckConnectionStatus.isInternetAvailable(mContext)) {
            getActivateAPI(query)
//            if (dbManager.getActiveATP(mContext).data != null) {
//                list.addAll(dbManager.getActiveATP(requireContext()).data)
//                if (list.size > 0) {
//                    if (txtNoTickets != null) {
//                        txtNoTickets.visibility = View.GONE
//                    }
//                    list.distinctBy { it.ticket }
//                    adapter =
//                        SupervisorActivateTicketsAdapter(list, mContext, object :
//                            SupervisorActivateTicketsAdapter.OnClickListenerArrivalDeparture {
//                            override fun onClick(position: Int, from: Int) {
//                                showDialog(position, from)
//                            }
//                        })
//                    recyclerViewActivate.adapter = adapter
//                } else {
//                    if (txtNoTickets != null) {
//                        txtNoTickets.visibility = View.VISIBLE
//                    }
//                }
//            }
        } else {
            if (dbManager.getActiveATP(mContext).data != null) {
                list.addAll(dbManager.getActiveATP(requireContext()).data)
                if (list.size > 0) {
                    if (txtNoTickets != null) {
                        txtNoTickets.visibility = View.GONE
                    }
                    list.distinctBy { it.ticket }
                    adapter =
                        SupervisorActivateTicketsAdapter(
                            list,
                            mContext,
                            object :
                                SupervisorActivateTicketsAdapter.OnClickListenerArrivalDeparture {
                                override fun onClick(position: Int, from: Int) {
                                    showDialog(position, from)
                                }
                            })
                    recyclerViewActivate.adapter = adapter
                } else {
                    if (txtNoTickets != null) {
                        txtNoTickets.visibility = View.VISIBLE
                    }
                }
            }
            mContext.makeToast(resources.getString(R.string.no_internet_connection))
        }

        layout_btn.setOnClickListener {
            val intent = Intent(activity, SelfParticipationOfflineActivity::class.java)
            startActivity(intent)
        }

        flvOfflineTicketClaim.setOnClickListener {
            SelectArrivalDepartureDialog(mContext).show()
        }
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        swipeLayout.setOnRefreshListener {
            val query = HashMap<String, Any>()
            query["apiToken"] = preference.getString("token")
            query["rsm"] = rsmId
            query["asm"] = asmId
            query["bdo_id"] = flId
            query["dd_id"] = ddId
            query["product"] = productId
            query["crop"] = cropId
            query["from_date"] = fromDate
            query["to_date"] = endDate
            if (CheckConnectionStatus.isInternetAvailable(mContext)) {
                getActivateAPI(query)
                multipleFileUploadWebservice()
            }
            swipeLayout.isRefreshing = false
        }
    }

    private val mMessageReceiver = object : BroadcastReceiver() {
        override fun onReceive(context: Context, data: Intent) {
            data.let {
                cropId = it.getStringExtra(Constants.CROP_ID) ?: ""
                productId = it.getStringExtra(Constants.PRODUCT_ID) ?: ""
                fromDate = it.getStringExtra(Constants.FROM_DATE) ?: ""
                endDate = it.getStringExtra(Constants.TO_DATE) ?: ""
                ddId = it.getStringExtra(Constants.PRODUCT_NAME) ?: ""
                rsmId = it.getStringExtra("RSMID") ?: ""
                asmId = it.getStringExtra("ASMID") ?: ""
                flId = it.getStringExtra("FLID") ?: ""
            }

            val query = HashMap<String, Any>()
            query["apiToken"] = preference.getString("token")
            query["rsm"] = rsmId
            query["asm"] = asmId
            query["bdo_id"] = flId
            query["dd_id"] = ddId
            query["product"] = productId
            query["crop"] = cropId
            query["from_date"] = fromDate
            query["to_date"] = endDate
            if (CheckConnectionStatus.isInternetAvailable(mContext)) {
                getActivateAPI(query)
            } else {
                mContext.makeToast(resources.getString(R.string.no_internet_connection))
            }
        }
    }

    private fun getActivateAPI(model: HashMap<String, Any>) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .SupervisorActivatedTickets(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SupervisorActivateBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                }

                override fun onNext(homeBean: SupervisorActivateBean) {
                    if (homeBean.statusCode == 200) {
                        insertDataInDataBase(homeBean)
                    } else {
                        if (homeBean.statusCode == 409) {
                            EventBus.getDefault().post("logout")
                        }

                    }
                }
            })
    }

    private fun insertDataInDataBase(homeBean: SupervisorActivateBean) {
        var allArrivalDepartureData = ArrayList<ArrivalDepartureModelOffline>()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                allArrivalDepartureData = appDatabase.arrivalDepartureParticipationDao()
                    .getAllArrivalDepartureData() as ArrayList<ArrivalDepartureModelOffline>
            }.run {
                withContext(Dispatchers.Main) {
                    list.clear()
                    if (!allArrivalDepartureData.isNullOrEmpty()) {
                        for ((index) in homeBean.data.withIndex()) {
                            if (homeBean.data[index].status == 0) {
                                for ((indexWhere) in allArrivalDepartureData.withIndex()) {
                                    if (homeBean.data[index].tID.equals(
                                            allArrivalDepartureData[indexWhere].ticket_id,
                                            ignoreCase = true
                                        )
                                    ) {
                                        if (allArrivalDepartureData[indexWhere].departure_time == null) {
                                            homeBean.data[index].isArrivalDeparture = 1
                                            list.add(homeBean.data[index])
                                        } else {
                                            homeBean.data[index].isArrivalDeparture = 2
                                            list.add(homeBean.data[index])
                                        }
                                    } else {
                                        list.add(homeBean.data[index])
                                    }
                                }
                            } else {
                                homeBean.data[index].isArrivalDeparture = 2
                                list.add(homeBean.data[index])
                            }
                        }
                    } else {
                        for ((index) in homeBean.data.withIndex()) {
                            if (homeBean.data[index].status == 1) {
                                homeBean.data[index].isArrivalDeparture = 2
                                list.add(homeBean.data[index])
                            } else {
                                list.add(homeBean.data[index])
                            }
                        }
                    }

                    dbManager.AddDataInActiveTableBox(mContext, homeBean.data)
                    if (list.isNotEmpty()) {
                        if (txtNoTickets != null) {
                            txtNoTickets.visibility = View.GONE
                        }
                        list.distinctBy { it.ticket }
                        adapter = SupervisorActivateTicketsAdapter(
                            list,
                            mContext,
                            object :
                                SupervisorActivateTicketsAdapter.OnClickListenerArrivalDeparture {
                                override fun onClick(position: Int, from: Int) {
                                    showDialog(position, from)
                                }
                            })
                        if (recyclerViewActivate != null) {
                            recyclerViewActivate.adapter = adapter
                        }
                    } else {
                        if (txtNoTickets != null) {
                            txtNoTickets.visibility = View.VISIBLE
                        }
                        if (adapter != null) {
                            adapter?.notifyDataSetChanged()
                        }
                    }
                }
            }
        }
    }

    private fun showDialog(postion: Int, isArrivalDeparture: Int) {
        if (latitude == null || longtitude == null) {
            getLocation()
            return
        }

        val ticketDate = list[postion].datetime

        var sdf = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
        val strDate: Date = sdf.parse(ticketDate)!!
        sdf = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
        val date = sdf.format(strDate)
        val currentDate = sdf.format(Calendar.getInstance(Locale.ENGLISH).time)
        if (currentDate != date) {
            Toast.makeText(requireContext(), "This Ticket is not for today", Toast.LENGTH_LONG)
                .show()
            return
        }
        val dialog = Dialog(mContext)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_add_feedback)
        dialog.setCanceledOnTouchOutside(true)
        dialog.setCancelable(true)
        val msg = dialog.findViewById<View>(R.id.edtFeedback) as EditText
        val tvOkay = dialog.findViewById<View>(R.id.btnSubmit) as Button
        val captureButton = dialog.findViewById<View>(R.id.txtCaptureImage) as TextView
        val imgView = dialog.findViewById<View>(R.id.imgView) as ImageView

        val ticketData = list[postion]

        imgView.setOnClickListener {
            PermissionsUtil.askPermissions(
                mContext,
                PermissionsUtil.CAMERA,
                PermissionsUtil.STORAGE
            ) {
                if (it) {
                    ImagePickerUtil.pickFromCamera2(easyImage, mContext, { imageFile, _ ->
                        file = ImageUtils.compressToExistingFile(
                            imageFile.absolutePath,
                            imageFile
                        )
                        if (file == null) {
                            file = imageFile
                        }
                        imgView.setImageBitmap(FileUtility.createBitmapFromFile(file))
                    }, "")
                }
            }
        }

        captureButton.setOnClickListener {
            PermissionsUtil.askPermissions(
                mContext,
                PermissionsUtil.CAMERA,
                PermissionsUtil.STORAGE
            ) {
                if (it) {
                    ImagePickerUtil.pickFromCamera2(easyImage, mContext, { imageFile, _ ->
                        file = ImageUtils.compressToExistingFile(
                            imageFile.absolutePath,
                            imageFile
                        )

                        if (file == null) {
                            file = imageFile
                        }
                        imgView.setImageBitmap(FileUtility.createBitmapFromFile(file))
                    }, "")
                }
            }
        }

        tvOkay.setOnClickListener {
            if (latitude == null || longtitude == null) {
                getLocation()
                Toast.makeText(
                    requireContext(),
                    "Unable to fetch Current Location. Try Again!!",
                    Toast.LENGTH_LONG
                ).show()
            } else {
                val sdfFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
                val dateFormat = sdfFormat.format(Calendar.getInstance(Locale.ENGLISH).timeInMillis)
                var model = ArrivalDepartureModelOffline()
                if (file != null) {
                    try {
                        GlobalScope.launch {
                            withContext(Dispatchers.IO) {
                                model = appDatabase.arrivalDepartureParticipationDao()
                                    .getArrivalDepartureData(ticketData.tID, ticketData.ticket)
                                    ?: ArrivalDepartureModelOffline()
                            }.run {
                                withContext(Dispatchers.Main) {
                                    if (isArrivalDeparture == 1) {
                                        model.token = preference.getString("token")
                                        model.ticket_id = ticketData.tID
                                        model.ticket_no = ticketData.ticket
                                        model.isOnline = "1"
                                        model.comment = msg.text.toString()
                                        model.imageURL = file!!.absolutePath
                                        model.latitude = latitude.toString()
                                        model.longitude = longtitude.toString()
                                        model.type = 1
                                        list[postion].isArrivalDeparture = 1
                                        model.arrival_time = dateFormat.toString()
                                        val currentHitTimeTwoHour = System.currentTimeMillis()
                                        model.lastHitTime = currentHitTimeTwoHour
                                    } else {
                                        if (file?.absolutePath != model.imageURL) {
                                            model.departure_comment = msg.text.toString()
                                            model.departure_imageURL = file!!.absolutePath
                                            model.departure_latitude = latitude.toString()
                                            model.departure_longitude = longtitude.toString()
                                            model.type = 3
                                            list[postion].isArrivalDeparture = 2
                                            model.departure_time = dateFormat.toString()
                                            val diff =
                                                Utility.findDifference(
                                                    sdfFormat,
                                                    model.arrival_time,
                                                    model.departure_time
                                                )
                                            model.duration = "$diff"
                                        } else {
                                            mContext.makeToast("Please select image to proceed.")
                                        }
                                    }
                                }.run {
                                    withContext(Dispatchers.IO) {
                                        if (isArrivalDeparture == 2 && model.departure_time != null) {
                                            DatabaseClient.getInstance(requireContext()).appDatabase
                                                .arrivalDepartureParticipationDao()
                                                .updateArrivalDepartureData(
                                                    model.id,
                                                    model.type,
                                                    model.departure_time,
                                                    model.departure_comment,
                                                    model.departure_latitude,
                                                    model.departure_longitude,
                                                    model.departure_imageURL,
                                                    model.duration,
                                                    model.lastHitTime
                                                )
                                        } else if (isArrivalDeparture == 1) {
                                            DatabaseClient.getInstance(requireContext()).appDatabase
                                                .arrivalDepartureParticipationDao()
                                                .insert(model)
                                        }
                                    }.run {
                                        withContext(Dispatchers.Main) {
                                            if (adapter != null) {
                                                adapter?.notifyDataSetChanged()
                                                dbManager.AddDataInActiveTableBox(mContext, list)
                                                if (isArrivalDeparture == 2 && model.departure_time != null) {
                                                    mContext.makeToast("Your departure data has been saved.")
                                                    logEventArrivalDepartureData(
                                                        "Departure data saved | Ticket No : ${model.ticket_no}",
                                                        Gson().toJson(model),
                                                        false
                                                    )
                                                } else if (isArrivalDeparture == 1) {
                                                    mContext.makeToast("Your arrival data has been saved.")
                                                    logEventArrivalDepartureData(
                                                        "Arrival data saved  | Ticket No : ${model.ticket_no}",
                                                        Gson().toJson(model),
                                                        false
                                                    )
                                                } else if (model.departure_time == null) {
                                                    mContext.makeToast("Please select image to proceed.")
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
                        requireContext(),
                        "Please select image to proceed.",
                        Toast.LENGTH_LONG
                    ).show()
                }
            }
        }

        dialog.show()
    }

    private fun logEventArrivalDepartureData(s: String, query: String?, b: Boolean) {
        val logModel = LogModel()
        logModel.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
        val data = "Arrival Departure Data : $query"
        logModel.apiData = data
        logModel.apiName = s
        val userData = (preference.getString("user_id") + "_"
                + preference.getString("ecn") + "_"
                + preference.getString("user_type") + "_"
                + preference.getString("Username") + "_"
                + preference.getString("designation"))
        logModel.userData = "\nUSER DATA : -> $userData"
        AppLogFile.writeLineInFile(Gson().toJson(logModel), preference.getString("ecn"))
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        easyImage?.handleActivityResult(
            requestCode,
            resultCode,
            data,
            mContext,
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

    private fun multipleFileUploadWebservice() {
        val offlineDatabase = OfflineDatabase(mContext)
        offlineDatabase.uploadArrivalDepartureData()
    }

    private fun getLocation() {
        checkPermission(
            arrayOf(Manifest.permission.ACCESS_FINE_LOCATION),
            object : ParentActivity.PermissionListener {
                override fun onPermissionStatus(status: Int) {
                    if (status == com.dhanuka.indhan.Activity.Constants.PERMISSION_GRANTED)
                        onLocationPermissionGranted()
                    else if (status == com.dhanuka.indhan.Activity.Constants.PERMISSION_SET_NEVER_AGAIN)
                        showPermissionSettingDialog()
                }
            })
    }

    private fun onLocationPermissionGranted() {
        if (ActivityCompat.checkSelfPermission(
                requireActivity(),
                Manifest.permission.ACCESS_FINE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(
                requireActivity(),
                Manifest.permission.ACCESS_COARSE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            return
        }

        val loc = requireContext().getSystemService(Context.LOCATION_SERVICE) as LocationManager
        if (!loc.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
            Toast.makeText(requireContext(), "Please Enable GPS.", Toast.LENGTH_LONG).show()
            return
        }
        fusedLocationClient.lastLocation.addOnSuccessListener(requireActivity()) { location ->
            if (location != null) {
                userLocation = location
                latitude = userLocation?.latitude
                longtitude = userLocation?.longitude
            } else {
                userLocation = null
                SmartLocation.with(requireContext()).location().start {
                    if (it != null) {
                        latitude = it.latitude
                        longtitude = it.longitude
                    }
                }
                Toast.makeText(
                    requireContext(),
                    "Not Able to Fetch Current Location. Try Again!!",
                    Toast.LENGTH_LONG
                ).show()
            }
        }
    }
}