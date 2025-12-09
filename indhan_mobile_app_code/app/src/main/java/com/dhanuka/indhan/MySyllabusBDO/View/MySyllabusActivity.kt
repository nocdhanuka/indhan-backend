package com.dhanuka.indhan.MySyllabusBDO.View

import android.annotation.SuppressLint
import android.os.Bundle
import android.os.Handler
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.LoginActivity
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.DbStructure.MyContentObserver
import com.dhanuka.indhan.DataBase.TableDataModel.MySyllabusDBModel
import com.dhanuka.indhan.DataBase.TableStructureModels.MySyllabusForBDOTable
import com.dhanuka.indhan.DataSyncServices.DataSyncManager
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.PlanATP.View.PlanAtpActivity
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.MySyllabusAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_my_syllabus.*
import org.joda.time.DateTime
import java.util.*


/**
 * Created by John on 5/14/2018.
 */
class MySyllabusActivity : BaseActivity(), SyncServiceInterfaceFactory.FailureListener,
    MyContentObserver.DbValueChangeListener, MySyllabusAdapter.onClickSyllabusListener,
    SwipeRefreshLayout.OnRefreshListener {


    private lateinit var mySyllabusAdapter: MySyllabusAdapter
    private lateinit var mySyllabusList: ArrayList<MySyllabusDBModel>
    private lateinit var observer: MyContentObserver
    var userType = ""
    var bdoId = 0
    lateinit var preference: Preference

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_my_syllabus)
        val bun = intent.extras
        if (bun != null) {
            bdoId = bun.getInt("bdo_id")
        }
        if (bdoId != 0) {
            layout_btn.visibility = View.GONE
            DatabaseManager.deleteAllMySyllabusData(this)
        }
        preference = Preference(this)
        userType = preference.getString("user_type")

        mySyllabusList = DatabaseManager.getAllMySyllabusList(this)
        iCross.setOnClickListener(this)
        plan_atp.setOnClickListener(this)

        rcViewMySyllabus.layoutManager =
            LinearLayoutManager(this)
        mySyllabusAdapter = MySyllabusAdapter(mySyllabusList)
        rcViewMySyllabus.adapter = mySyllabusAdapter
        swipe_layout.setOnRefreshListener(this)
        mySyllabusAdapter.setOnclickListener(this)
        observer = MyContentObserver()
        observer.setListener(this)
        applicationContext.contentResolver.registerContentObserver(
            MySyllabusForBDOTable.CONTENT_URI,
            true,
            observer
        )
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            showProgress()
            val manager = DataSyncManager(this, this)
            manager.getMySyllabus(bdoId)
        }
    }

    @SuppressLint("NotifyDataSetChanged")
    override fun OnDbValueChange() {
        runOnUiThread {
            mySyllabusList.clear()
            val list = DatabaseManager.getAllMySyllabusList(this)
            mySyllabusList.addAll(list)
            mySyllabusList.sort()
            mySyllabusList.reverse()

            if (mySyllabusList.size > 0) {
                rcViewMySyllabus.visibility = View.VISIBLE
                tv_nodata.visibility = View.GONE
            } else {
                rcViewMySyllabus.visibility = View.GONE
                tv_nodata.visibility = View.VISIBLE
            }
            mySyllabusAdapter.notifyDataSetChanged()
        }

    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
            R.id.plan_atp -> {
                if (!userType.equals("ZONAL", false) && !userType.contains("REGIONAL")) {
                    launchIntent(PlanAtpActivity::class.java, false)
                }
            }
        }
    }

    override fun onInvalidAttempt() {
        val preference = Preference(this)
        preference.putString("ticketId", "")
        preference.putString("ticketTime", "")
        preference.putBolean("isLogin", false)
        preference.putString("token", "")
        launchIntent(LoginActivity::class.java, true)
    }


    override fun onResume() {
        super.onResume()
        OnDbValueChange()

    }

    override fun onSyllabusSelected(syllabus: MySyllabusDBModel) {
        if (!userType.equals("ZONAL", false) && !userType.contains("REGIONAL")) {
            val calendar = java.util.Calendar.getInstance( Locale.ENGLISH)
            val d = calendar.get(java.util.Calendar.MONTH) + 1
            if (syllabus.year == DateTime.now().year) {
                if (syllabus.month >= d) {
                    val b = Bundle()
                    b.putSerializable("model", syllabus)
                    launchIntent(PlanAtpActivity::class.java, b, false)
                } else {
                    makeToast("These are previous month Plans")
                }
            } else if (syllabus.year > DateTime.now().year) {
                val b = Bundle()
                b.putSerializable("model", syllabus)
                launchIntent(PlanAtpActivity::class.java, b, false)
            }
        }
    }

    override fun onDBSyncSuccess() {
        hideProgress()
    }

    override fun onRefresh() {
        Handler().postDelayed({ swipe_layout.isRefreshing = false }, 3000)
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            val manager = DataSyncManager(this, this)
            manager.getMySyllabus(bdoId)
        }
    }

    override fun onFailure(msg: String) {

    }

}
