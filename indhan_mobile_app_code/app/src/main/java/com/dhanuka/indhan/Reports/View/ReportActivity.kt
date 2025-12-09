package com.dhanuka.indhan.Reports.View

import android.content.Intent
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.BuildConfig
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Reports.Model.EfficiencyResponse
import com.dhanuka.indhan.Reports.Model.ReportsResponse
import com.dhanuka.indhan.Reports.Reports_MVP.ReportsFactory
import com.dhanuka.indhan.Reports.Reports_MVP.ReportsPresenterImpl
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_reports_all.*
import org.greenrobot.eventbus.EventBus
import java.text.DecimalFormat
import java.util.*
import kotlin.collections.ArrayList
import kotlin.collections.HashMap

/**
 * Created by John on 5/7/2018.
 */
class ReportActivity : BaseActivity(), ReportsFactory.ReportsHomeListener {


    lateinit var preference: Preference
    var userType = ""
    var userId = ""
    var apiToken = ""
    var startDate = ""
    var endDate = ""
    var product = 0
    var manager_id = 0
    var sr_manager_id = 0
    var s1_id = 0
    var s1_name = ""
    var manager_level = ""
    var product_name = ""
    var manager_name = ""
    var sr_manager_name = ""
    var efficient_date = ""
    var efficiency = ""

    lateinit var presenter: ReportsFactory.ReportsPresenter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_reports_all)
        preference = Preference(this)
        timendate_tv.text = Utility.monthnYear
        userType = preference.getString("user_type")
        apiToken = preference.getString("token")
        efficient_date = preference.getString("efficient_date")
        efficiency = preference.getString("efficiency")
        percent.text = "$efficiency %"

        presenter = ReportsPresenterImpl(this)
        filter.setOnClickListener(this)
        llBack.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        enable_left.setOnClickListener(this)
        enable_right.setOnClickListener(this)
        getData()
    }

    var list_efficieny: ArrayList<EfficiencyResponse.Efficiency> = ArrayList()
    var curr = 0
    var prev = 0
    override fun onEfficiencySuccess(model: EfficiencyResponse) {
        if (model != null) {
            if (model.statusCode == 409) {
                EventBus.getDefault().post("logout")
                return
            }
            val values = model.data.efficiency
            values.forEach {
                it.dateObj = Utility.getDateFromMonth(it.year + "-" + it.month)
            }
            values.sort()
            //values.reverse()
            list_efficieny.clear()
            list_efficieny.addAll(values)
            curr = list_efficieny.size - 1
            prev = curr - 1
            showEfficiency()
        } else {
            list_efficieny.clear()
            showEfficiency()
        }
        /* preference.putString("efficient_date", model.data.date)
         preference.putString("efficiency", model.data.efficiency)
         efficiency = model.data.efficiency
         efficient_date = model.data.date
         percent.text = efficiency + "%"*/
    }

    private fun showEfficiency() {
        if (list_efficieny.size > 0) {
            if (prev < 0) {
                disable_left.visibility = View.VISIBLE
                enable_left.visibility = View.GONE
            } else {
                disable_left.visibility = View.GONE
                enable_left.visibility = View.VISIBLE
            }
            if (curr >= (list_efficieny.size - 1)) {
                disable_right.visibility = View.VISIBLE
                enable_right.visibility = View.GONE
            } else {
                disable_right.visibility = View.GONE
                enable_right.visibility = View.VISIBLE
            }
            percent.text = list_efficieny.get(curr).efficiency + "%"
            timendate_tv.text = Utility.month_and_year(list_efficieny.get(curr).dateObj)
        } else {
            disable_left.visibility = View.VISIBLE
            enable_left.visibility = View.GONE
            enable_right.visibility = View.GONE
            disable_right.visibility = View.VISIBLE
            percent.text = "0 %"
            timendate_tv.text = Utility.month_and_year(Date())
        }
    }

    override fun onEfficiencyFailure(model: String) {
        if (!model.isNullOrEmpty())
            makeToast(model)
    }

    private fun getData() {

        val map = HashMap<String, Any>()
        map.put("apiToken", apiToken)
        map.put("startDate", startDate)
        map.put("endDate", endDate)
        if (product == 0)
            map.put("product", "")
        else
            map.put("product", product.toString())
        if (manager_id == 0)
            map.put("manager_id", "")
        else
            map.put("manager_id", manager_id.toString())

        map.put("manager_level", manager_level)
        map.put("appversion", BuildConfig.VERSION_NAME)

        if (CheckConnectionStatus.isInternetAvailable(this)) {
            presenter.onRequestReportsData(map)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.filter -> {
                // launchIntent(FilterActivity::class.java, false)
                val intent = Intent(this, FilterActivity::class.java)
                val b = Bundle()
                b.putString("startDate", startDate)
                b.putString("endDate", endDate)
                b.putString("manager_level", manager_level)
                b.putInt("product", product)
                b.putString("product_name", product_name)
                b.putInt("manager_id", manager_id)
                b.putString("manager_name", manager_name)
                b.putInt("sr_manager_id", sr_manager_id)
                b.putString("sr_manager_name", sr_manager_name)
                b.putInt("s1_id", s1_id)
                b.putString("s1_name", s1_name)
                intent.putExtra("bundle", b)
                startActivityForResult(intent, 1001)
            }
            R.id.llBack -> {
                finish()
            }
            R.id.ivBack -> {
                finish()
            }
            R.id.enable_left -> {
                if (prev >= 0) {
                    prev--
                    curr--
                }
                showEfficiency()
            }
            R.id.enable_right -> {
                if (curr < list_efficieny.size - 1) {
                    prev++
                    curr++
                }
                showEfficiency()
            }
        }
    }

    override fun onSuccess(model: ReportsResponse) {
        if (userType.equals("BDO")) {
            layout_active_bd_s1.visibility = View.GONE
            layout_activities.visibility = View.VISIBLE
            if (model.data.totalActivities != null)
                activities_tv.text = model.data.totalActivities.toString()
        } else if (userType.equals("AREA")) {
            layout_active_bd_s1.visibility = View.VISIBLE
            layout_activities.visibility = View.GONE
            if (model.data.bDOCount == null) {
                model.data.bDOCount = 0
            }
            if (model.data.totalActivities == null) {
                model.data.totalActivities = "0"
            }
            if (model.data.totalActivities == "0" || model.data.bDOCount == 0) {
                bdo_count_tv.text = "0"
            } else {
                var decimalFormat = DecimalFormat("#.##")
                var test: Float =
                    model.data.totalActivities.toFloat() / model.data.bDOCount.toFloat()
                bdo_count_tv.text = decimalFormat.format(test)
            }

        } else if (userType.equals("REGIONAL")) {
            layout_active_bd_s1.visibility = View.VISIBLE
            var areaCount = 0
            layout_activities.visibility = View.GONE
            if (model.data.areaCount == null) {
                areaCount = model.data.areaCount.toInt()
            }
            if (model.data.totalActivities == null) {
                model.data.totalActivities = "0"
            }
            if (model.data.totalActivities == "0" || areaCount == 0) {
                bdo_count_tv.text = "0"
            } else {
                bdo_count_tv.text = (model.data.totalActivities.toInt() / areaCount).toString()
            }

        } else if (userType.equals("ZONAL")) {
            layout_active_bd_s1.visibility = View.VISIBLE
            var regionalCount = 0
            layout_activities.visibility = View.GONE
            if (model.data.areaCount == null) {
                regionalCount = model.data.regionalCount.toInt()
            }
            if (model.data.totalActivities == null) {
                model.data.totalActivities = "0"
            }
            if (model.data.totalActivities == "0" || regionalCount == 0) {
                bdo_count_tv.text = "0"
            } else {
                bdo_count_tv.text =
                    (model.data.totalActivities.toInt() / regionalCount).toString()
            }
        }

        if (userType.equals("AREA")) {
            banner_name.text = "Active BDO"
            if (model.data.bDOCount != null)
                s1_count_tv.text = model.data.bDOCount.toString()
        } else if (userType.equals("REGIONAL")) {
            banner_name.text = "Active S1"
            if (model.data.areaCount != null)
                s1_count_tv.text = model.data.areaCount.toString()
        } else if (userType.equals("ZONAL")) {
            banner_name.text = "Active S2"
            if (model.data.regionalCount != null)
                s1_count_tv.text = model.data.regionalCount.toString()
        }
        if (model.data.totalFarmers != null)
            total_farmers_tv.text = model.data.totalFarmers.toString()
        if (model.data.totalValidatedFarmers != null)
            total_validate_farmers_tv.text = model.data.totalValidatedFarmers.toString()
        if (model.data.averageFarmers != null)
            avg_farmers.text = model.data.averageFarmers.toString()
        if (model.data.averageValidateFarmers != null)
            avg_validated_farmers.text = model.data.averageValidateFarmers.toString()
        if (model.data.testimonial != null)
            testimonial.text = model.data.testimonial.toString()
        if (!model.data.closedCount.isNullOrEmpty())
            close_queries_ban.text = model.data.closedCount
        if (!model.data.activeCount.isNullOrEmpty())
            open_queries.text = model.data.activeCount
        if (!model.data.followUpCount.isNullOrEmpty())
            follow_up_queries.text = model.data.followUpCount
        if (!model.data.closedSaleCount.isNullOrEmpty())
            close.text = model.data.closedSaleCount
        if (!model.data.activeSaleCount.isNullOrEmpty())
            open.text = model.data.activeSaleCount
        if (!model.data.followSaleUpCount.isNullOrEmpty())
            follow_up.text = model.data.followSaleUpCount

        evaluated.text = model.data.demoEvaluated.toString()
        awaited.text = model.data.demoAwaited.toString()
        overDue.text = model.data.demoOverdue.toString()


        txtTotalACP.text = model.data.totalACPFamer.toString()
        txtTotalAcrerageValue.text = model.data.totalAcres.toString()
        txtTotalLeadValue.text = model.data.totalLeads.toString()
        txtTotalSoldValue.text = model.data.totalClosedAcres.toString()


        if (CheckConnectionStatus.isInternetAvailable(this)) {
            presenter.onRequestEfficiency(apiToken, efficient_date)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }

    }

    override fun onFailure(mes: String) {
        makeToast(mes)
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            presenter.onRequestEfficiency(apiToken, efficient_date)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }

    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (data != null) {
            data.getBundleExtra("bundle")?.let {
                startDate = it.getString("startDate") ?: ""
                endDate = it.getString("endDate") ?: ""
                product = it.getInt("product", 0)
                manager_id = it.getInt("manager_id", 0)
                manager_level = it.getString("manager_level") ?: ""
                manager_name = it.getString("manager_name") ?: ""
                product_name = it.getString("product_name") ?: ""
                sr_manager_id = it.getInt("sr_manager_id", 0)
                sr_manager_name = it.getString("sr_manager_name") ?: ""
                s1_id = it.getInt("s1_id", 0)
                s1_name = it.getString("s1_name") ?: ""
                getData()
            }
        }

    }
}
