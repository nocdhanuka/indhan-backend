package com.dhanuka.indhan.ACPVisitHistory

import android.content.Intent
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.VillageSpinnerDialog
import com.dhanuka.indhan.MarkVisits.MarkVisitBean
import com.dhanuka.indhan.MarkVisits.MarkVisitContractor
import com.dhanuka.indhan.MarkVisits.MarkVisitPresenter
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.farmerlist.FarmerBean
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import com.google.firebase.analytics.FirebaseAnalytics
import kotlinx.android.synthetic.main.activity_acpvisit_filter.*
import kotlinx.android.synthetic.main.activity_earning_filtering.txtMonth
import kotlinx.android.synthetic.main.activity_earning_filtering.txtVillageList
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

class ACPVisitFilterActivity : BaseActivity(), MarkVisitContractor.MarkVisitView {
    override fun setItems(items: MarkVisitBean) {

    }

    override fun setVillageList(items: MyVillageBean) {
        villageList = items.data as ArrayList<MyVillageBean.MyVillageData>
        villageSpinnerDialog.updatelist(villageList)
    }

    override fun setVillageList(items: FarmerBean) {

    }

    override fun failure(items: String) {
        logEventCustom(items, "v2/villageList")
    }

    private fun logEventCustom(mes: String?, apiName: String) {
        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
        bundle["Api_Name"] = apiName
        if (mes != null) {
            bundle["error"] = mes
        } else {
            bundle["error"] = "unknown error occurred"
        }
        bundle["user_detail"] = preference.getString("user_id")
            .plus("_${preference.getString("ecn")}")
            .plus("_${preference.getString("user_type")}")
            .plus(
                "_${preference.getString("Username")}"
                    .plus("_${preference.getString("designation")}")
            )
        Utility.logEvent("ACPVisitFilterActivity", "failed_api_$apiName", bundle)
    }

    override fun onClick(v: View?) {

    }

    private lateinit var villageSpinnerDialog: VillageSpinnerDialog
    private var villageList: ArrayList<MyVillageBean.MyVillageData> = java.util.ArrayList()
    var villageId = ""
    var fromDate = ""
    var toDate = ""
    private lateinit var mPresenter: MarkVisitContractor.MarkVisitPresenter
    private lateinit var preference: Preference

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_acpvisit_filter)

        mPresenter = MarkVisitPresenter(this)
        preference = Preference(this)

        val query = HashMap<String, Any>()
        query["ECN"] = preference.getString("ecn")
        query["apiToken"] = preference.getString("token")
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            mPresenter.onVillageList(query)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }



        villageSpinnerDialog = VillageSpinnerDialog(this, villageList, "Select Village")
        villageSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtVillageList.text = villageList[position].villageName
                villageId = villageList[position].villageId
            }
        })


        txtVillageList.setOnClickListener {
            if (!villageSpinnerDialog.isEnable)
                villageSpinnerDialog.showSpinerDialog()
        }

        txtMonth.setOnClickListener {

            Utility.getPickedDate(this, object : Utility.DatePickCallBack {
                override fun onCancel() {

                }

                override fun onDateSet(date: String?, formattedDate: String?) {
                    txtMonth.text = date
                    fromDate = date!!
                }
            })

        }

        txtMonthOne.setOnClickListener {
            Utility.getPickedDate(this, object : Utility.DatePickCallBack {
                override fun onCancel() {

                }

                override fun onDateSet(date: String?, formattedDate: String?) {
                    txtMonthOne.text = date
                    toDate = date!!
                }
            })
        }


        btnRbSubmit.setOnClickListener {
            if (fromDate != "" && toDate != "") {
                if (compareDate(fromDate, toDate)) {
                    callingActivity?.let {
                        val i = Intent(this@ACPVisitFilterActivity, it::class.java)
                        i.putExtra("villageId", villageId)
                        i.putExtra("toDate", toDate)
                        i.putExtra("fromDate", fromDate)
                        setResult(100, i)
                        finish()
                    }
                } else {
                    makeToast("Please select future date for end date")
                }
            } else {
                makeToast("Please select start date and end date")
            }

        }


    }

    fun compareDate(date: String, date1: String): Boolean {
        val sdf = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
        try {
            val mDate = sdf.parse(date)
            val mDate1 = sdf.parse(date1)
            return mDate.time - mDate1.time <= 0

        } catch (e: ParseException) {
            e.printStackTrace()
        }

        return false
    }
}
