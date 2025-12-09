package com.dhanuka.indhan.SelfParticipartionOffline

import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.DataBase.TableDataModel.ArrivalDepartureModelOffline
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.AppLogFile
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.LogModel
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_self_participation2.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.joda.time.DateTime

class SelfParticipationOfflineActivity : BaseActivity() {
    private lateinit var appDatabase: AppDatabase
    private var arraySelfList = ArrayList<ArrivalDepartureModelOffline>()
    private var adapter: SelfParticipationOfflineAdapter? = null
    private lateinit var mActivity: SelfParticipationOfflineActivity

    override fun onClick(v: View) {
        when (v.id) {
            R.id.ivBack -> {
                onBackPressed()
            }

            R.id.ivDownload -> {
                if (CheckConnectionStatus.isInternetAvailable(mActivity)) {
                    multipleFileUploadWebservice()
                } else {
                    makeToast(resources.getString(R.string.no_internet_connection))
                }
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_self_participation2)
        mActivity = this
        appDatabase = DatabaseClient.getInstance(mActivity).appDatabase
        ivBack.setOnClickListener(this)
        ivDownload.setOnClickListener(this)
        getOfflineData()
    }

    private fun getOfflineData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val dataGot =
                    appDatabase.arrivalDepartureParticipationDao().getAllArrivalDepartureData()
                if (dataGot != null) {
                    if (dataGot.isNotEmpty()) {
                        arraySelfList = dataGot as ArrayList<ArrivalDepartureModelOffline>
                    }
                }
            }.run {
                withContext(Dispatchers.Main) {
                    if (arraySelfList.size > 0) {
                        logEventArrivalDepartureData(
                            "All Arrival departure ticket Data",
                            Gson().toJson(arraySelfList),
                            false
                        )
                        txtNoTickets.visibility = View.GONE
                        recyclerViewActivate.visibility = View.VISIBLE
                        adapter =
                            SelfParticipationOfflineAdapter(arraySelfList, mActivity, object :
                                OnClickListener {
                                override fun onClick(position: Int) {
                                }
                            })
                        recyclerViewActivate.layoutManager =
                            LinearLayoutManager(this@SelfParticipationOfflineActivity)
                        recyclerViewActivate.adapter = adapter
                        adapter?.notifyDataSetChanged()
                    } else {
                        txtNoTickets.visibility = View.VISIBLE
                        recyclerViewActivate.visibility = View.GONE
                    }
                }
            }
        }

    }

    private fun logEventArrivalDepartureData(s: String, query: String?, b: Boolean) {
        val logModel = LogModel()
        logModel.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
        val data = " All Arrival Departure Data : $query"
        logModel.apiData = data
        logModel.apiName = s
        val userData = (Preference.getInstance(this).getString("user_id") + "_"
                + Preference.getInstance(this).getString("ecn") + "_"
                + Preference.getInstance(this).getString("user_type") + "_"
                + Preference.getInstance(this).getString("Username") + "_"
                + Preference.getInstance(this).getString("designation"))
        logModel.userData = "\nUSER DATA : -> $userData"
        AppLogFile.writeLineInFile(
            Gson().toJson(logModel),
            Preference.getInstance(this).getString("ecn")
        )
    }


    private fun multipleFileUploadWebservice() {
        val offlineDatabase = OfflineDatabase(mActivity)
        offlineDatabase.resetArrivalDepartureData()
        Handler(Looper.getMainLooper()).postDelayed({
            finish()
        }, 1000)
        makeToast("Your data will be uploaded in background. Please check after sometime.")
    }
}
