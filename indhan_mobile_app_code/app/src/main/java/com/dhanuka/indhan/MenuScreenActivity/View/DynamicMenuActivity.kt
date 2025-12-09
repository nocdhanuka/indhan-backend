package com.dhanuka.indhan.MenuScreenActivity.View

import android.Manifest
import android.annotation.SuppressLint
import android.annotation.TargetApi
import android.app.Activity
import android.content.Context
import android.content.Intent
import android.content.pm.PackageInfo
import android.content.pm.PackageManager
import android.location.Location
import android.location.LocationManager
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.provider.Settings
import android.telephony.TelephonyManager
import android.text.TextUtils
import android.util.Log
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import androidx.core.content.PermissionChecker
import androidx.recyclerview.widget.GridLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.ACPVisitHistory.ACPVisitHistoryActivity
import com.dhanuka.indhan.ATPMenu.activity.AtpMenuActivity
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.SupervisorATPActivity
import com.dhanuka.indhan.Activity.DataCollection.distributor.DataCollectionActivity
import com.dhanuka.indhan.Activity.LoginActivity
import com.dhanuka.indhan.Activity.TicketHistoryActivity
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel
import com.dhanuka.indhan.Activity.misAndReports.MisAndReportsActivity
import com.dhanuka.indhan.BDOPlanFilterActivity.View.BDOPlanFilterActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.DbStructure.MyContentObserver
import com.dhanuka.indhan.DataSyncServices.DataSyncManager
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.DemoRequestActivity.activity.DemoRequestsActivity.DemoRequestsActivity
import com.dhanuka.indhan.DemoRequestEvaluated.activities.DemoRequestEvaluatedActivity
import com.dhanuka.indhan.DemoRequestEvaluated.activities.DemoRequestMenuActivity
import com.dhanuka.indhan.MarkVisits.MarkVisitActivity
import com.dhanuka.indhan.MenuScreenActivity.Adapter.MenuCategoryAdapter
import com.dhanuka.indhan.MenuScreenActivity.DynamicMenuBaseActivity
import com.dhanuka.indhan.MenuScreenActivity.Factory.MenuScreenFactory
import com.dhanuka.indhan.MenuScreenActivity.MenuPresenter
import com.dhanuka.indhan.MenuScreenActivity.Model.AllLocation
import com.dhanuka.indhan.MenuScreenActivity.Model.MenuDBModel
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.MenuScreenActivity.ProgressListener
import com.dhanuka.indhan.MyLeadMenu.activities.myleadactivity.MyLeadActivity
import com.dhanuka.indhan.MyVillage.MyVillageActivity
import com.dhanuka.indhan.NewTicketActivity.NewTicketActivity
import com.dhanuka.indhan.PlanATP.View.*
import com.dhanuka.indhan.PlanSyllabusForBdo.View.PlanSyllabusActivity
import com.dhanuka.indhan.PlanSyllabusForBdo.View.SyllabusHistoryActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.Profile.ProfileActivity
import com.dhanuka.indhan.R
import com.dhanuka.indhan.RegisterFarmerScreen.FarmerRegistrationMenuActivity
import com.dhanuka.indhan.Reports.View.ReportActivity
import com.dhanuka.indhan.Reports.supervisor_report.SupervisorReportsActivity
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.TestimonialList.TestimonialListActivity
import com.dhanuka.indhan.TestimonialList.supervisor.TestimonialSupervisorActivity
import com.dhanuka.indhan.View.OfflineProgressDialog
import com.dhanuka.indhan.acpfarmers.AcpFarmerActivity
import com.dhanuka.indhan.activities.ActivitiesActivity
import com.dhanuka.indhan.institutional.activity.InstitutionalActivity
import com.dhanuka.indhan.myteam.MyTeamActivity
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.support.RetroInstance
import com.dhanuka.indhan.support.RetrofitService
import com.dhanuka.indhan.support.model.ManCampaignResponseModel
import com.dhanuka.indhan.support.model.MandatoryModel
import com.dhanuka.indhan.support.view.QaActivity
import com.dhanuka.indhan.support.view.SupportActivity
import com.dhanuka.indhan.teamsummary.activities.TeamSummaryActivity
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.ForceUpdateChecker
import com.dhanuka.indhan.util.Utility
import com.google.android.material.snackbar.Snackbar
import com.google.android.play.core.appupdate.AppUpdateManager
import com.google.android.play.core.appupdate.AppUpdateManagerFactory
import com.google.android.play.core.install.InstallStateUpdatedListener
import com.google.android.play.core.install.model.AppUpdateType.IMMEDIATE
import com.google.android.play.core.install.model.InstallStatus
import com.google.android.play.core.install.model.UpdateAvailability
import com.google.firebase.database.*
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_dynamic_menu.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.greenrobot.eventbus.EventBus
import org.greenrobot.eventbus.Subscribe
import org.greenrobot.eventbus.ThreadMode
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

/**
 * Created by John on 5/10/2018.
 */
class DynamicMenuActivity : DynamicMenuBaseActivity(),
    SyncServiceInterfaceFactory.FailureListener,
    ForceUpdateChecker.OnUpdateNeededListener,
    MenuScreenFactory.MenuViewListener,
    MenuCategoryAdapter.MenuClickListener,
    MyContentObserver.DbValueChangeListener {

    private var myRefLog: DatabaseReference? = null
    private var listeners: ValueEventListener? = null
    private var listenersLog: ValueEventListener? = null
    private var myRef: DatabaseReference? = null
    private var fbDatabase: FirebaseDatabase? = null
    private var isMenuInDB: Boolean = true
    private var appUpdateManager: AppUpdateManager? = null
    private val pERMISSION_cODE = 725
    private val REQUEST_CODE_UPDATE = 101
    private var preference: Preference? = null
    lateinit var presenter: MenuScreenFactory.MenuPresenterListener
    lateinit var adapter: MenuCategoryAdapter
    var mLastLocation: Location? = null
    var token: String = ""
    var fb: String = ""
    var userType: String = ""
    var ecn: String = ""
    var list: ArrayList<MenuDBModel> = ArrayList()
    var mandCampList: ArrayList<MandatoryModel> = ArrayList()
    private var mLocationManager: LocationManager? = null
    private val LOCATION_INTERVAL: Long = 5000
    private val LOCATION_DISTANCE = 10f

    private var mLocationListeners = arrayOf(
        LocationListener(LocationManager.GPS_PROVIDER),
        LocationListener(LocationManager.NETWORK_PROVIDER)
    )

    private val requiredPermissions = arrayOf(
        Manifest.permission.CAMERA,
        Manifest.permission.READ_EXTERNAL_STORAGE,
        Manifest.permission.WRITE_EXTERNAL_STORAGE,
        Manifest.permission.ACCESS_COARSE_LOCATION,
        Manifest.permission.ACCESS_FINE_LOCATION,
        Manifest.permission.READ_PHONE_STATE
    )

    private fun initializeLocationManager() {
        if (mLocationManager == null) {
            mLocationManager =
                applicationContext.getSystemService(Context.LOCATION_SERVICE) as LocationManager
        }
    }

    override fun onRestart() {
        super.onRestart()
        for (l in list) {
            if (l.name.toLowerCase().trim() == "support") {
                getManCampaigns(this)
            }
        }
    }

    lateinit var manager: DataSyncManager

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_dynamic_menu)
        fbDatabase = FirebaseDatabase.getInstance()
        myRef = fbDatabase?.reference?.child("indhan_user")
        txtTeamSummary.setOnClickListener {
            val intent = Intent(this, TeamSummaryActivity::class.java)
            startActivity(intent)
        }
        presenter = MenuPresenter(this)
        preference = Preference(this)
        ("Home - " + preference?.getString("Username")).also { title_home.text = it }
        ("Version - " + com.dhanuka.indhan.BuildConfig.VERSION_NAME).also { version.text = it }
        userType = preference?.getString("user_type") ?: ""
        ecn = preference?.getString("ecn") ?: ""
        if (userType.equals("BDO", true)) {
            profile.text = preference?.getString("designation") ?: ""
        } else {
            profile.text = preference?.getString("user_type") ?: ""
        }
        if (userType.equals("general", true)) {
            getLocalData(false)
        } else {
            checkLocalStateList()
        }
        rvList.layoutManager = GridLayoutManager(
            this,
            2
        ) as RecyclerView.LayoutManager?
        adapter = MenuCategoryAdapter(list, this)
        adapter.setClickListener(this)
        tvMeForgotPassword.setOnClickListener(this)

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            checkPermissionToExecute(requiredPermissions, pERMISSION_cODE)
        } else {
            getlatLocation()
        }
        rvList.adapter = adapter
        presenter.getMenuFromDBRequest()

        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getAppdata()
        } else {
            offlineListData()
        }

        runOnUiThread {
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                val menulist: ArrayList<MenuDBModel> = DatabaseManager.getAllMenuList(this)
                isMenuInDB = menulist.isNotEmpty()
                showProgress()
                manager = DataSyncManager(this, this)
                token = Preference.getInstance(this).getString("token")
                fb = Preference.getInstance(this).getTokenString("fb")
                if (token.isNotEmpty()) {
                    //call api for menu updation
                    manager.updateMenuInDb(token, fb, isMenuInDB)
                    when {
                        userType.equals("general", false) -> {
                        }
                        else -> {
                            manager.upDateProductCropList(token)
                            if (userType.equals("AREA", false))
                                manager.getSharedSyllabusHistory(token)
                            manager.getPlanActivity(token)
                        }
                    }
                }
                ForceUpdateChecker.with(this).onUpdateNeeded(this).check()
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
        }

    }

    private fun addFirebaseListeners() {
        listeners = object : ValueEventListener {
            override fun onDataChange(snapshot: DataSnapshot) {
                if (snapshot.exists()) {
                    val value: String? = snapshot.getValue(String::class.java)
                    if (!TextUtils.isEmpty(value)) {
                        if (value?.contains(",") == true) {
                            val result: ArrayList<String> =
                                value.split(",").map { it.trim() } as ArrayList<String>
                            result.forEach() {
                                if (ecn.equals(it, ignoreCase = true)) {
                                    Utility.exportDatabase(true, ecn)
                                    Utility.exportDatabase(false, ecn)
                                }
                            }.also {
                                if (result.contains(ecn)) {
                                    result.remove(ecn)
                                    myRef?.setValue(result.joinToString(","))
                                }
                            }
                        } else {
                            if (ecn.equals(value, ignoreCase = true)) {
                                Utility.exportDatabase(true, ecn)
                                Utility.exportDatabase(false, ecn)
                                myRef?.setValue("")
                            }
                        }
                    }
                }
            }

            override fun onCancelled(error: DatabaseError) {}
        }
        listeners?.let {
            myRef?.addValueEventListener(it)
        }

        // Log File upload

        myRefLog = fbDatabase?.reference?.child("log_file")

        listenersLog = object : ValueEventListener {
            override fun onDataChange(snapshot: DataSnapshot) {
                if (snapshot.exists()) {
                    val value: String? = snapshot.getValue(String::class.java)
                    if (!TextUtils.isEmpty(value)) {
                        if (value?.contains(",") == true) {
                            val result: ArrayList<String> =
                                value.split(",").map { it.trim() } as ArrayList<String>
                            result.forEach() {
                                if (ecn.equals(it, ignoreCase = true)) {
                                    Utility.exportLogFile(ecn)
                                }
                            }.also {
                                if (result.contains(ecn)) {
                                    result.remove(ecn)
                                    myRefLog?.setValue(result.joinToString(","))
                                }
                            }
                        } else {
                            if (ecn.equals(value, ignoreCase = true)) {
                                Utility.exportLogFile(ecn)
                                myRefLog?.setValue("")
                            }
                        }
                    }
                }
            }

            override fun onCancelled(error: DatabaseError) {}
        }

        listenersLog?.let {
            myRefLog?.addValueEventListener(it)
        }

    }

    private fun offlineListData() {
        val menulist: ArrayList<MenuDBModel> = DatabaseManager.getAllMenuList(this)
        list.clear()
        for (item in menulist.indices) {
            menulist[item].idInt = menulist[item].id.toInt()
        }
        menulist.sortBy { it.idInt }
        list.addAll(menulist)
        adapter.notifyDataSetChanged()
        hideProgress()
    }

    private fun checkLocalStateList() {
        rx.Observable.fromCallable {
            val appDatabase = DatabaseClient.getInstance(this).appDatabase
            appDatabase.stateDao().numberOfRows
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
            return@onErrorReturn 0
        }.subscribe {
            getLocalData(it == 0)
        }
    }

    private fun getLocalData(isLoaderShown: Boolean) {
        val progressDialog = OfflineProgressDialog()
        if (!isLoaderShown) {
            addFirebaseListeners()
        }
        try {
            OfflineDatabase(this).checkOfflineDatabase(
                isLoaderShown,
                object : ProgressListener {
                    override fun showProgressDialog() {
                        runOnUiThread {
                            val transaction = supportFragmentManager.beginTransaction();
                            transaction.add(progressDialog, "Progress")
                            transaction.commitAllowingStateLoss()
                        }
                    }

                    override fun dismissProgressDialog() {
                        if (!isFinishing) {
                            runOnUiThread {
                                if (progressDialog != null) {
                                    if (progressDialog.isAdded && !progressDialog.isHidden) {
                                        progressDialog.dismissAllowingStateLoss()
                                    }
                                    addFirebaseListeners()
                                }
                            }
                        }
                    }

                    override fun updateProgress(progress: Int) {
                        runOnUiThread {
                            if (progressDialog.isAdded && !progressDialog.isHidden && progressDialog.isVisible)
                                progressDialog.setProgress(progress)
                        }
                    }
                })
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    override fun onClick(p0: View?) {
        when (p0?.id) {
            R.id.tvMeForgotPassword -> {
                if (CheckConnectionStatus.isInternetAvailable(this)) {
                    showProgress()
                    logoutUser()
                } else {
                    Toast.makeText(
                        this,
                        getString(R.string.no_internet_connection),
                        Toast.LENGTH_LONG
                    ).show()
                }
            }
        }
    }

    private fun clearRoomDatabaseData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                DatabaseClient.getInstance(this@DynamicMenuActivity).appDatabase.clearAllTables()
            }.run {
                withContext(Dispatchers.Main) {
                    launchIntent(LoginActivity::class.java, false)
                    finishAffinity()
                }
            }
        }
    }

    override fun onGetMenuFromDBResult(menu: ArrayList<MenuDBModel>) {
        for (item in menu.indices) {
            menu[item].idInt = menu[item].id.toInt()
        }
        list.clear()
        list.addAll(menu)
        adapter.notifyDataSetChanged()
        hideProgress()
    }

    private fun updateAppVersion() {
        val dialog = AlertDialog.Builder(this)
            .setTitle("InDhan needs an update")
            .setMessage("To use this App, Please download the latest version.").setCancelable(false)
            .setPositiveButton(
                "Update"
            ) { dialog, which -> redirectStore("https://play.google.com/store/apps/details?id=com.dhanuka.indhan") }
            .setNegativeButton(
                "No, thanks"
            ) { dialog, which -> finish() }.create()

        if (if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
                !this@DynamicMenuActivity.isDestroyed
            } else {
                false
            }
        ) {
            dialog.show()
        }
    }

    override fun onClick(view: View, menuBean: MenuDBModel) {
        when (menuBean.name.toLowerCase().trim()) {

            "profile summary" -> {
                launchIntent(ProfileActivity::class.java, false)
            }
            "plan syllabus" -> {
                launchIntent(PlanSyllabusActivity::class.java, false)
            }
            "syllabus history" -> {
                if (userType.equals("ZONAL", false) || userType.contains("REGIONAL")) {
                    val b = Bundle()
                    b.putString("menu", "syllabus")
                    launchIntent(BDOPlanFilterActivity::class.java, b, false)
                } else
                    launchIntent(SyllabusHistoryActivity::class.java, false)
            }
            "customer care" -> {
                if (userType.equals("BDO", false)) {
                    launchIntent(CustomerCareActivity::class.java, false)
                } else
                    launchIntent(CustomerCareS1Activity::class.java, false)
            }
            "reports" -> {
                if (userType.equals("BDO", false)) {
                    launchIntent(ReportActivity::class.java, false)
                } else {
                    launchIntent(SupervisorReportsActivity::class.java, false)
                }
            }
            "farmers registration" -> {
                val bundle = Bundle()
                bundle.putString("image", menuBean.image_url)
                launchIntent(FarmerRegistrationMenuActivity::class.java, bundle, false)
            }
            "ticket history" -> {
                launchIntent(TicketHistoryActivity::class.java, false)
            }
            "new ticket" -> {
                launchIntent(NewTicketActivity::class.java, false)
            }
            "create ticket" -> {
                launchIntent(NewTicketActivity::class.java, false)
            }
            "atp" -> {
                if (userType.equals("BDO", false)) {
                    launchIntent(AtpMenuActivity::class.java, false)
                } else {
                    launchIntent(SupervisorATPActivity::class.java, false)
                }
            }
            "testimonials" -> {
                if (userType.equals("BDO", false) || userType.equals("dhanuka_doctor", false)) {
                    launchIntent(TestimonialListActivity::class.java, false)
                } else {
                    launchIntent(TestimonialSupervisorActivity::class.java, false)
                }
            }
            "my syllabus" -> {
//                launchIntent(MySyllabusActivity::class.java, false)
                if (!userType.equals("ZONAL", false) && !userType.contains("REGIONAL")) {
                    launchIntent(PlanAtpActivity::class.java, false)
                }
            }
            "activate ticket" -> {
                launchIntent(ActivateTicketActivity::class.java, false)
            }
            "self participation" -> {
                launchIntent(SelfParticipationActivity::class.java, false)
            }
            "mark visits" -> {
                launchIntent(MarkVisitActivity::class.java, false)
            }
            "my villages" -> {
                launchIntent(MyVillageActivity::class.java, false)
            }
            "acp visit history" -> {
                launchIntent(ACPVisitHistoryActivity::class.java, false)
            }
            "bdo plan" -> {
                if (userType.equals("ZONAL", false) || userType.contains("REGIONAL")) {
                    val b = Bundle()
                    b.putString("menu", "bdo")
                    launchIntent(BDOPlanFilterActivity::class.java, b, false)
                } else
                    launchIntent(BDOPlanActivity::class.java, false)
            }
            "demo requests" -> {
                launchIntent(DemoRequestsActivity::class.java, false)
            }
            "demo request" -> {
                launchIntent(DemoRequestEvaluatedActivity::class.java, false)
            }
            "demo" -> {
                val bundle = Bundle()
                bundle.putString("image", menuBean.image_url)
                launchIntent(DemoRequestMenuActivity::class.java, false)
            }
            "my leads" -> {
                launchIntent(MyLeadActivity::class.java, false)
            }
            "atp/invited farmers" -> {
                launchIntent(AtpMenuActivity::class.java, false)
            }
            "team summary" -> {
                launchIntent(TeamSummaryActivity::class.java, false)
            }
            "my team" -> {
                if (userType.equals("BDO", false)) {
                    launchIntent(MyTeamActivity::class.java, false)
                } else {
                    launchIntent(TeamSummaryActivity::class.java, false)
                }
            }
            "acp farmer" -> {
                launchIntent(AcpFarmerActivity::class.java, false)
            }
            "support" -> {
                launchIntent(SupportActivity::class.java, false)
            }
            "activities" -> {
                launchIntent(ActivitiesActivity::class.java, false)
            }
            "dropbox" -> {
                val browserIntent = Intent(
                    Intent.ACTION_VIEW,
                    Uri.parse("http://www.dhanukainfosystem.in/testimonial/")
                )
                startActivity(browserIntent)
            }
            "data collection" -> {
                launchIntent(DataCollectionActivity::class.java, false)
            }
            "mis and reports" -> {
                launchIntent(MisAndReportsActivity::class.java, false)
            }
            "institutional" -> {
                launchIntent(InstitutionalActivity::class.java, false)
            }
        }
    }

    override fun OnDbValueChange() {

    }

    override fun onInvalidAttempt() {
        clearEveryThing()
    }

    @TargetApi(Build.VERSION_CODES.M)
    private fun checkPermissionToExecute(permissions: Array<String>, requestCode: Int) {

        val camera = ContextCompat.checkSelfPermission(
            this,
            permissions[0]
        ) != PackageManager.PERMISSION_GRANTED
        val reactd = ContextCompat.checkSelfPermission(
            this,
            permissions[1]
        ) != PackageManager.PERMISSION_GRANTED
        val write = ContextCompat.checkSelfPermission(
            this,
            permissions[2]
        ) != PackageManager.PERMISSION_GRANTED
        val location = ContextCompat.checkSelfPermission(
            this,
            permissions[3]
        ) != PackageManager.PERMISSION_GRANTED
        val fine_location = ContextCompat.checkSelfPermission(
            this,
            permissions[4]
        ) != PackageManager.PERMISSION_GRANTED
        val read_phone = ContextCompat.checkSelfPermission(
            this,
            permissions[5]
        ) != PackageManager.PERMISSION_GRANTED

        if (camera || reactd || write || location || fine_location || read_phone) {
            requestPermissions(permissions, requestCode)
        } else {
            getlatLocation()
        }
    }

    private fun getManCampaigns(context: Context) {
        val retroInstance = RetroInstance.getRetrofitInstance().create(RetrofitService::class.java)
        val call = retroInstance.getManCampaigns(ecn)
        call.enqueue(object : Callback<ManCampaignResponseModel> {
            override fun onResponse(
                call: Call<ManCampaignResponseModel>,
                response: Response<ManCampaignResponseModel>
            ) {
                if (response.body() != null) {
                    val responseBody = response.body()!!
                    if (responseBody.status == 200 && responseBody.data?.size!! > 0) {
                        mandCampList = responseBody.data as ArrayList<MandatoryModel>
                        responseBody.message?.let {
                            ShowMandatoryDialog(it)
                        }
                    }
                } else {
                    Toast.makeText(
                        context,
                        "Server error. Please try again after sometime or with moderate internet connection.",
                        Toast.LENGTH_LONG
                    ).show()
                }
            }

            override fun onFailure(call: Call<ManCampaignResponseModel>, t: Throwable) {
                print("Mandatory Error: $t")
            }
        })
    }

    @TargetApi(Build.VERSION_CODES.M)
    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        if (permissions.isNotEmpty()) {
            if (requestCode == pERMISSION_cODE) {
                if (grantResults[0] == PermissionChecker.PERMISSION_GRANTED && grantResults[1] == PermissionChecker.PERMISSION_GRANTED && grantResults[2] == PermissionChecker.PERMISSION_GRANTED && grantResults[3] == PermissionChecker.PERMISSION_GRANTED && grantResults[4] == PermissionChecker.PERMISSION_GRANTED && grantResults[5] == PermissionChecker.PERMISSION_GRANTED) {
                    getlatLocation()
                } else {
                    AlertDialog.Builder(this)
                        .setMessage("The app needs these permissions to work, Exit?")
                        .setTitle("Permission Denied")
                        .setCancelable(false)
                        .setPositiveButton("Retry") { dialog, which ->
                            dialog.dismiss()
                            checkPermissionToExecute(
                                requiredPermissions,
                                pERMISSION_cODE/*, logsRunnable*/
                            )
                        }
                        .setNegativeButton("Exit App") { dialog, which ->
                            dialog.dismiss()
                            finish()
                        }.show()
                }
            }
        }
    }

    override fun onFailure(msg: String) {
        offlineListData()
    }

    private fun insertData(): MenuDBModel {
        return MenuDBModel(
            id = "4312",
            name = "Institutional",
            target = "none",
            image_url = "https://udf.vga.mybluehostin.me/dhanuka/assets/appmenu/ticket_history.png",
            notification_count = 0,
            menu_show = 1
        )
    }

    override fun onDBSyncSuccess() {
        runOnUiThread {
            val menulist: java.util.ArrayList<MenuDBModel> = DatabaseManager.getAllMenuList(this)
            list.clear()
            for (item in menulist.indices) {
                menulist[item].idInt = menulist[item].id.toInt()
            }
            menulist.sortBy { it.idInt }
            list.addAll(menulist)
            adapter.notifyDataSetChanged()
            hideProgress()

            for (l in list) {
                if (l.name.toLowerCase().trim() == "support") {
                    getManCampaigns(this)
                }
            }

            try {
                val pInfo: PackageInfo = packageManager.getPackageInfo(packageName, 0)
                val version: String = pInfo.versionName
                val onlineVersion = preference?.getString(Constants.VERSION_NAME) ?: "5.33"
                if (!TextUtils.isEmpty(version) && !TextUtils.isEmpty(onlineVersion)) {
                    if (version.toDouble() < onlineVersion.toDouble())
                        updateAppVersion()
                }
            } catch (e: PackageManager.NameNotFoundException) {
                e.printStackTrace()
            }
        }
    }

    override fun onUpdateNeeded(updateUrl: String, currentVersion: String) {
        val dialog = AlertDialog.Builder(this)
            .setTitle("New Indhan Ver.$currentVersion available")
            .setMessage("Please, update Indhan app to new version to continue.")
            .setPositiveButton(
                "Update"
            ) { dialog, which -> redirectStore(updateUrl) }.setNegativeButton(
                "No, thanks"
            ) { _, _ -> finish() }.create()
        dialog.show()
    }

    private fun redirectStore(updateUrl: String) {
        val intent = Intent(Intent.ACTION_VIEW, Uri.parse(updateUrl))
        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
        startActivity(intent)
    }

    /* get all state, district,tehsil and village */
    private fun getAppdata() {
        val query = HashMap<String, Any>()
        query["apiToken"] = preference?.getString("token") ?: ""
        query["app_version"] = com.dhanuka.indhan.BuildConfig.VERSION_NAME
        query["device_id"] = getUniqueIMEIId(this)
        query["device_name"] = (Build.MANUFACTURER + " " + Build.MODEL)
        query["device_os"] =
            (Build.VERSION.RELEASE + " " + Build.VERSION_CODES::class.java.fields[Build.VERSION.SDK_INT].name)
        query["lat_location"] = preference?.getString(Constants.latitude) ?: ""
        query["long_location"] = preference?.getString(Constants.longitude) ?: ""
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .allLocations(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<AllLocation>() {
                override fun onNext(t: AllLocation?) {
                    preference?.putString("location", Gson().toJson(t))
                }

                override fun onCompleted() {
                }

                override fun onError(e: Throwable?) {
                }

            })

    }

    override fun onDestroy() {
        super.onDestroy()
        listeners?.let {
            myRef?.removeEventListener(it)
        }
        listeners = null

        listenersLog?.let {
            myRefLog?.removeEventListener(it)
        }
        listenersLog = null
    }

    override fun onResume() {
        super.onResume()
        appUpdateManager = AppUpdateManagerFactory.create(this)
        appUpdateManager!!.registerListener(installStateUpdatedListener)
        appUpdateManager!!.appUpdateInfo
            .addOnSuccessListener {
                when {
                    it.updateAvailability() == UpdateAvailability.DEVELOPER_TRIGGERED_UPDATE_IN_PROGRESS -> {
                        appUpdateManager!!.startUpdateFlowForResult(
                            it,
                            IMMEDIATE,
                            this,
                            REQUEST_CODE_UPDATE
                        )
                    }
                    it.installStatus() == InstallStatus.DOWNLOADED -> {
                        popupSnackbarForCompleteUpdate();
                    }
                    else -> {
                        Log.e("appUpdateManager", "checkForAppUpdateAvailability: something else");
                    }
                }
            }
        checkGps()
    }

    /* Displays the snackbar notification and call to action. */
    private fun popupSnackbarForCompleteUpdate() {
        Snackbar.make(
            findViewById(R.id.activity_main_layout),
            "An update has just been downloaded.",
            Snackbar.LENGTH_INDEFINITE
        ).apply {
            setAction("RESTART") { appUpdateManager!!.completeUpdate() }
            setActionTextColor(resources.getColor(R.color.colorAccent))
            show()
        }
    }

    private val installStateUpdatedListener = InstallStateUpdatedListener {
        when {
            it.installStatus() == InstallStatus.DOWNLOADED -> {
                popupSnackbarForCompleteUpdate()
            }
            it.installStatus() == InstallStatus.INSTALLED -> {
                deregistorLister()
            }
            else -> {
                Log.i(
                    "appUpdateManager",
                    "InstallStateUpdatedListener: state: " + it.installStatus()
                );
            }
        }
    }

    private fun deregistorLister() {
        appUpdateManager!!.unregisterListener(installStateUpdatedListener)
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == REQUEST_CODE_UPDATE) {
            if (resultCode != RESULT_OK) {
                Log.e("appUpdateManager", "onActivityResult: app download failed");
            }
        }
    }

    @SuppressLint("ServiceCast")
    private fun getUniqueIMEIId(context: Context): String {
        try {
            val telephonyManager =
                context.getSystemService(Context.TELEPHONY_SERVICE) as TelephonyManager
            if (ActivityCompat.checkSelfPermission(
                    context,
                    Manifest.permission.READ_PHONE_STATE
                ) !== PackageManager.PERMISSION_GRANTED
            ) {
                return ""
            }
            val imei = telephonyManager.deviceId
            return if (imei != null && !imei.isEmpty()) {
                imei
            } else {
                Build.SERIAL
            }
        } catch (e: Exception) {
            e.printStackTrace()
        }
        return "not_found"
    }

    private fun getlatLocation() {
        initializeLocationManager()
        try {
            mLocationManager?.requestLocationUpdates(
                LocationManager.NETWORK_PROVIDER,
                LOCATION_INTERVAL,
                LOCATION_DISTANCE,
                mLocationListeners[1]
            )
        } catch (ex: SecurityException) {
            Log.i("john", "fail to request location update, ignore", ex)
        } catch (ex: IllegalArgumentException) {
            Log.d("john", "network provider does not exist, " + ex.message)
        }

        try {
            mLocationManager?.requestLocationUpdates(
                LocationManager.GPS_PROVIDER,
                LOCATION_INTERVAL,
                LOCATION_DISTANCE,
                mLocationListeners[0]
            )
        } catch (ex: SecurityException) {
            Log.i("john", "fail to request location update, ignore", ex)
        } catch (ex: IllegalArgumentException) {
            Log.d("john", "gps provider does not exist " + ex.message)
        }
    }

    private inner class LocationListener(provider: String) : android.location.LocationListener {

        init {
            mLastLocation = Location(provider)
        }

        override fun onLocationChanged(location: Location) {
            mLastLocation?.set(location)
//            Log.e("location", "lat-" + mLastLocation?.latitude.toString())
//            Log.e("location", "long-" + mLastLocation?.longitude.toString())
            preference?.putString(Constants.latitude, mLastLocation?.latitude.toString())
            preference?.putString(Constants.longitude, mLastLocation?.longitude.toString())
        }

        override fun onProviderDisabled(provider: String) {
        }

        override fun onProviderEnabled(provider: String) {
        }

        override fun onStatusChanged(provider: String, status: Int, extras: Bundle) {
        }
    }

    fun launchIntent2(
        cls: Class<out Activity?>?,
        id: Int,
        isFilled: Boolean,
        campName: String,
        finish: Boolean
    ) {
        val intent = Intent(this, cls)
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
        intent.putExtra("id", id)
        intent.putExtra("campName", campName)
        intent.putExtra("isFilled", isFilled)
        startActivity(intent)
        if (finish) finish()
    }

    fun ShowMandatoryDialog(message: String) {
        if (!isFinishing) {
            val dialog = AlertDialog.Builder(this)
                .setTitle("Alert")
                .setMessage(message)
                .setCancelable(false)
                .setPositiveButton(
                    "Proceed"
                ) { dialog, which ->
                    launchIntent2(
                        QaActivity::class.java,
                        mandCampList[0].campId!!,
                        false,
                        mandCampList[0].campName.toString(),
                        false
                    )
                }.create()
            dialog.show()
        }
    }

    private fun logoutUser() {
        val hashMap: java.util.HashMap<String, Any> = java.util.HashMap()
        hashMap["apiToken"] = preference?.getString("token") ?: ""
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .logoutAPI(preference?.getString("token")).subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TicketHistoryModel>() {
                override fun onNext(it: TicketHistoryModel?) {
                    dismissProgress()
                    if (it?.statusCode == 200) {
                        Toast.makeText(this@DynamicMenuActivity, it.message, Toast.LENGTH_LONG)
                            .show()
                        clearEveryThing()
                    } else {
                        Toast.makeText(
                            this@DynamicMenuActivity,
                            it?.message ?: "Logout Successfully",
                            Toast.LENGTH_LONG
                        ).show()
                        clearEveryThing()
                    }
                }

                override fun onCompleted() {
                }

                override fun onError(e: Throwable?) {

                }

            })
    }

    private fun clearEveryThing() {
        preference?.putString("ticketId", "")
        preference?.putString("ticketTime", "")
        preference?.putBolean("isLogin", false)
        preference?.putString("token", "")
        preference?.clearAllData()
        DatabaseManager.deleteAllPlanAtp(this@DynamicMenuActivity)
        DatabaseManager.deleteAllMenuData(this@DynamicMenuActivity)
        DatabaseManager.deleteAllLanguageData(this@DynamicMenuActivity)
        DatabaseManager.deleteProductCropList(this@DynamicMenuActivity)
        DatabaseManager.deleteAllImageOfflineTable(this@DynamicMenuActivity)
        DatabaseManager.deleteAllMenuData(this@DynamicMenuActivity)
        DatabaseManager.deleteAllLanguageData(this@DynamicMenuActivity)
        DatabaseManager.deleteProductCropList(this@DynamicMenuActivity)
        DatabaseManager.deleteAllbdoListData(this@DynamicMenuActivity)
        DatabaseManager.deleteAllDataSourceData(this@DynamicMenuActivity)
        DatabaseManager.deleteAllStateData(this@DynamicMenuActivity)
        DatabaseManager.deleteAllMySyllabusData(this@DynamicMenuActivity)
        DatabaseManager.deleteAllCropData(this@DynamicMenuActivity)
        DatabaseManager.deleteAllProductData(this@DynamicMenuActivity)
        DatabaseManager.deleteAllProductCropData(this@DynamicMenuActivity)
        DatabaseManager.deleteAllBDOData(this@DynamicMenuActivity)
        clearRoomDatabaseData()
    }

    override fun onStart() {
        super.onStart()
        if (!EventBus.getDefault().isRegistered(this)) EventBus.getDefault().register(this)
    }

    override fun onStop() {
        super.onStop()
        EventBus.getDefault().unregister(this)
    }

    @Subscribe(threadMode = ThreadMode.MAIN)
    public fun onMessageEvent(event: String) {
        if (!TextUtils.isEmpty(event)) {
            when (event) {
                "logout" -> {
                    makeToast("Token expired please log in again.")
                    clearEveryThing()
                    finishAffinity()
                }
            }
        }
    }

    private fun checkGps() {
        val loc = getSystemService(Context.LOCATION_SERVICE) as LocationManager
        if (!loc.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
            gpsDialog()
        }
    }

    private fun gpsDialog() {
        val alertDialog = AlertDialog.Builder(this)
        alertDialog.setTitle("Enable GPS")
            .setMessage("Enable GPS for better experience of the App.")
            .setPositiveButton("Yes") { dialogInterface, i ->
                dialogInterface.dismiss()
                val intent1 = Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS);
                startActivity(intent1);
            }.setNegativeButton("No") { dialog, which ->
                dialog.dismiss()
                finish()
            }
            .setCancelable(false).create().show()
    }

}
