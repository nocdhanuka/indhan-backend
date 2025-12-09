package com.dhanuka.indhan.Activity.misAndReports

import android.annotation.SuppressLint
import android.app.Dialog
import android.app.DownloadManager
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.os.Environment
import android.text.TextUtils
import android.view.View
import android.view.Window
import android.widget.ArrayAdapter
import android.widget.ListPopupWindow
import androidx.core.content.FileProvider
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.misAndReports.model.MisReportsListResponse
import com.dhanuka.indhan.Activity.misAndReports.model.ReportsListData
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_mis_reports_list.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File
import java.text.SimpleDateFormat
import java.util.*
import kotlin.collections.set


class MisReportsListActivity : BaseActivity(), MisDataListAdapter.SubMenuClickListener {

    private var file2: File? = null
    private lateinit var preference: Preference
    private lateinit var appDataBaseClient: AppDatabase
    private lateinit var adapter: MisDataListAdapter
    private var listOfMenu = ArrayList<ReportsListData>()
    private var listOfYear = ArrayList<Int>()
    private var listOfMonth = ArrayList<Int>()
    private var listOfMonthString = ArrayList<String>()
    private var reportTypeId = 0
    private var year = ""
    private var month = ""
    private var updatedAt = "0"
    var monthData: Int = 0
    var yearData: Int = 0

    override fun onClick(v: View?) {

    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_mis_reports_list)
        preference = Preference(this)
        appDataBaseClient = DatabaseClient.getInstance(this).appDatabase
        reportTypeId = intent?.getIntExtra("report_type_id", 0) ?: 0
        imgFilter.setOnClickListener {
            openFilter()
        }
        getMenuListFromDb(true)
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            rx.Observable.fromCallable {
                "v3/getreportsList".getLastHitTimeData()
            }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
                return@onErrorReturn ApiTimeTableModel()
            }.subscribe { it ->
                it?.let { updatedAt = it.updatedAt ?: "0" }
                getMisReportsList(reportTypeId, updatedAt)
            }
        } else {
            getMenuListFromDb(true)
        }
    }

    private fun openFilter() {
        val dialog = Dialog(this@MisReportsListActivity)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_filter_reports)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val txtYear = dialog.findViewById<View>(R.id.txtYear) as Medium
        val txtMonth = dialog.findViewById<View>(R.id.txtMonth) as Medium
        if (yearData != 0) {
            txtYear.text = yearData.toString()
        }
        if (monthData != 0) {
            txtMonth.text = Utility.getMonth(monthData - 1)
        }
        txtYear.setOnClickListener {
            val yearPop = listOfYear.distinct().sorted()
            val listPopupAdapter = ArrayAdapter(
                this@MisReportsListActivity,
                android.R.layout.simple_spinner_dropdown_item,
                yearPop
            )
            val popupWindow = ListPopupWindow(this@MisReportsListActivity)
            popupWindow.setOnItemClickListener { parent, view, position, id ->
                yearData = yearPop[position]
                txtYear.text = yearPop[position].toString()
                popupWindow.dismiss()
            }

            popupWindow.setContentWidth(txtYear.width)
            popupWindow.height = ListPopupWindow.WRAP_CONTENT
            popupWindow.anchorView = txtYear
            popupWindow.setAdapter(listPopupAdapter)
            popupWindow.show()
        }


        txtMonth.setOnClickListener {
            listOfMonthString.clear()
            val monthList = listOfMonth.distinct().sorted()
            monthList.forEach() {
                listOfMonthString.add(Utility.getMonth(it - 1))
            }
            val listPopupAdapter = ArrayAdapter(
                this@MisReportsListActivity,
                android.R.layout.simple_spinner_dropdown_item,
                listOfMonthString
            )
            val popupWindow = ListPopupWindow(this@MisReportsListActivity)
            popupWindow.setOnItemClickListener { parent, view, position, id ->
                monthData = monthList[position]
                txtMonth.text = listOfMonthString[position]
                popupWindow.dismiss()
            }

            popupWindow.setContentWidth(txtMonth.width)
            popupWindow.height = ListPopupWindow.WRAP_CONTENT
            popupWindow.anchorView = txtMonth
            popupWindow.setAdapter(listPopupAdapter)
            popupWindow.show()
        }

        val tvOkay = dialog.findViewById<View>(R.id.tvSubmitFilter) as Medium
        val tvClearFilter = dialog.findViewById<View>(R.id.tvClearFilter) as Medium
        val tvNo = dialog.findViewById<View>(R.id.tvMeCancel) as Medium

        tvNo.setOnClickListener {
            dialog.dismiss()
        }

        tvClearFilter.setOnClickListener {
            yearData = 0
            monthData = 0
            getMenuListFromDb(true)
            dialog.dismiss()
        }

        tvOkay.setOnClickListener {
            if (yearData != 0 || monthData != 0) {
                setFilterMenu(yearData, monthData)
                dialog.dismiss()
            } else {
                makeToast("Please select year or month to filter.")
            }
        }
        dialog.show()

    }

    private fun setFilterMenu(yearData: Int, monthData: Int) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                if (yearData != 0 && monthData != 0) {
                    listOfMenu =
                        appDataBaseClient.misReportsListDao()
                            .getAllMisReportDataList(
                                reportTypeId,
                                yearData,
                                monthData
                            ) as ArrayList<ReportsListData>
                } else if (yearData != 0) {
                    listOfMenu =
                        appDataBaseClient.misReportsListDao()
                            .getAllMisYearReportDataList(
                                reportTypeId,
                                yearData
                            ) as ArrayList<ReportsListData>
                } else if (monthData != 0) {
                    listOfMenu =
                        appDataBaseClient.misReportsListDao()
                            .getAllMisMonthReportDataList(
                                reportTypeId,
                                monthData
                            ) as ArrayList<ReportsListData>
                }
            }.run {
                withContext(Dispatchers.Main) {
                    listOfMenu.sortWith(compareBy<ReportsListData> { it.year }.thenBy { it.month })
//                    listOfMenu.reversed()
                    setAdapter(false)
                }
            }
        }
    }

    private fun String.getLastHitTimeData(): ApiTimeTableModel? {
        return appDataBaseClient.apiTimeDao()
            .getApiTimeData(this, reportTypeId.toString())
    }

    private fun getMisReportsList(reportTypeId: Int, isUpdated: String) {
        if (listOfMenu.isEmpty()) {
            showProgress()
        }
        val query = HashMap<String, Any>()
        query["api_token"] = preference.getString("token")
        query["report_type"] = reportTypeId
        query["year"] = year
        query["month"] = month
        query["updated_at"] = isUpdated

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getMisReportsList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<MisReportsListResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: MisReportsListResponse) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        insetDataInDataBase(listBean.data)
                    } else {
                        if (listBean.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })
    }


    @SuppressLint("SimpleDateFormat")
    private fun insetDataInDataBase(data: List<ReportsListData>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data.forEach() {
                    appDataBaseClient.misReportsListDao().insert(it)
                }
                val timeData = "v3/getreportsList".getLastHitTimeData()
                val sdf = SimpleDateFormat(Constants.DATE_TIME_FORMAT)
                val currentDate = sdf.format(Date())
                if (timeData != null) {
                    appDataBaseClient.apiTimeDao()
                        .updateTimeInDb("v3/getreportsList", currentDate, reportTypeId.toString())
                } else {
                    val timeDataModel = ApiTimeTableModel()
                    timeDataModel.apiName = "v3/getreportsList"
                    timeDataModel.updatedAt = currentDate
                    timeDataModel.typeId = reportTypeId.toString()
                    appDataBaseClient.apiTimeDao().insert(timeDataModel)
                }
                appDataBaseClient.apiTimeDao()
                getMenuListFromDb(true)
            }
        }
    }


    private fun getMenuListFromDb(b: Boolean) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                listOfMenu =
                    appDataBaseClient.misReportsListDao()
                        .getAllMisActiveReportDataList(reportTypeId,1) as ArrayList<ReportsListData>
            }.run {
                withContext(Dispatchers.Main) {
                    listOfMenu.sortWith(compareBy<ReportsListData> { it.year }.thenBy { it.month }
                        .thenByDescending { it.updated_at })
                    setAdapter(b)
                }
            }

        }
    }

    private fun setAdapter(b: Boolean) {
        if (!listOfMenu.isNullOrEmpty()) {
            tvNoData.visibility = View.GONE
            rvMisReportListRecycler.visibility = View.VISIBLE
            adapter = MisDataListAdapter(this, listOfMenu)
            rvMisReportListRecycler.adapter = adapter
            adapter.setClickListener(this)
            if (b) {
                listOfYear.clear()
                listOfMonth.clear()
                listOfMenu.forEach() {
                    listOfYear.add(it.year)
                    listOfMonth.add(it.month)
                }
                listOfMonth.distinct().sorted()
                listOfYear.distinct().sorted()
            }
        } else {
            tvNoData.visibility = View.VISIBLE
            rvMisReportListRecycler.visibility = View.GONE
        }

    }

    override fun onClick(view: View, subMenu: ReportsListData) {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            if (!TextUtils.isEmpty(subMenu.uploaded_file)) {
                downloadFile("${Constants.IMAGEBASEURL}${subMenu.uploaded_file}")
            } else {
                makeToast("No File attached to download/View")
            }

//            if (!TextUtils.isEmpty(subMenu.uploaded_file)) {
//                val intent = Intent(this@MisReportsListActivity, WebViewAnyDataActivity::class.java)
//                intent.putExtra(
//                    "key",
//                    "$IMAGEBASEURL${subMenu.uploaded_file}"
//                )
//                startActivity(intent)
//            }

        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    private fun downloadFile(uploadedFile: String) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val downloadDir =
                    (this@MisReportsListActivity as Context).getExternalFilesDir(Environment.DIRECTORY_DOWNLOADS)
                val manager = getSystemService(DOWNLOAD_SERVICE) as DownloadManager
                val uri = Uri.parse(uploadedFile)
                val destinationPath = downloadDir?.absolutePath + "/Indhan Reports/"
                val image = "${uri.lastPathSegment}"
                val file = File(destinationPath)
                if (!file.exists()) {
                    file.mkdirs()
                }
                file2 = File(file, image)
                if (file2?.exists() == false) {
                    val request = DownloadManager.Request(uri)
                    request.setAllowedNetworkTypes(DownloadManager.Request.NETWORK_WIFI or DownloadManager.Request.NETWORK_MOBILE)
                    request.setTitle(uri.lastPathSegment)
                    request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED)
                    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                        request.setRequiresCharging(false)
                    }
                    request.setAllowedOverMetered(true)
                    request.setAllowedOverRoaming(true)
                    val fileUri = Uri.fromFile(file2)
                    request.setDestinationUri(fileUri)
                    withContext(Dispatchers.Main) {
                        manager.enqueue(request)
                        registerReceiver(
                            onComplete,
                            IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE)
                        )
                    }
                } else {
                    withContext(Dispatchers.Main) {
                        openFile(file2!!)
                    }
                }
            }
        }
    }

    var onComplete: BroadcastReceiver = object : BroadcastReceiver() {
        override fun onReceive(ctxt: Context, intent: Intent) {
            if (file2 != null) {
               openFile(file2!!)
            }
        }
    }

    private fun openFile(file2: File) {
        val uriData = FileProvider.getUriForFile(
            this@MisReportsListActivity,
            this@MisReportsListActivity.applicationContext.packageName + ".provider",
            file2
        )
        val mime = contentResolver.getType(uriData)
        try {
            val intent = Intent()
            intent.action = Intent.ACTION_VIEW
            intent.setDataAndType(uriData, mime)
            intent.addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
            startActivity(intent)
        } catch (e: Exception) {
            makeToast(
                "No application found to open this file, please download app to view file type ${
                    uriData?.lastPathSegment?.split(".")?.get(1) ?: ""
                }."
            )
        }
    }
}