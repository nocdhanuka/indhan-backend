package com.dhanuka.indhan.EarningFilter

import android.os.Bundle
import android.view.View
import android.widget.ArrayAdapter
import android.widget.ListPopupWindow
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.VillageSpinnerDialog
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import com.google.android.flexbox.FlexboxLayoutManager
import com.google.firebase.analytics.FirebaseAnalytics
import kotlinx.android.synthetic.main.activity_earning_filtering.*
import kotlinx.android.synthetic.main.activity_mark_visit.*
import java.util.*

class EarningFilteringActivity : BaseActivity(), EarningFilterContractor.EarningFilterView {
    override fun onClick(v: View?) {

    }

    override fun setItems(items: MyVillageBean) {
        villageList = items.data as ArrayList<MyVillageBean.MyVillageData>
        villageSpinnerDialog.updatelist(villageList)
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
        Utility.logEvent("EarningFilteringActivity", "failed_api_$apiName", bundle)
    }

    var month = 0
    private lateinit var villageSpinnerDialog: VillageSpinnerDialog
    private lateinit var preference: Preference
    private lateinit var mPresenter: EarningFilterContractor.EarningFilterPresenter
    private var villageList: ArrayList<MyVillageBean.MyVillageData> = ArrayList()
    private var selectedVillageList: ArrayList<MyVillageBean.MyVillageData> = ArrayList()
    private lateinit var adapter: SelctedVillagesAdapter

    private var villageId: String = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_earning_filtering)


        mPresenter = EarningFilterPresenter(this)
        preference = Preference(this)

        adapter = SelctedVillagesAdapter(this, selectedVillageList)
        recyclerViewVillages.layoutManager = FlexboxLayoutManager(this)
        recyclerViewVillages.adapter = adapter


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
                villageId = villageList[position].villageId
                //txtVillageList.text=villageList[position].villageName
                selectedVillageList.add(villageList[position])

                adapter.notifyDataSetChanged()
                updateList(position)


            }
        })


        txtVillageList.setOnClickListener {
            if (!villageSpinnerDialog.isEnable)
                villageSpinnerDialog.showSpinerDialog()
        }


        var list = mutableListOf<String>()
        list.add("January")
        list.add("February")
        list.add("March")
        list.add("April")
        list.add("May")
        list.add("June")
        list.add("July")
        list.add("August")
        list.add("September")
        list.add("October")
        list.add("November")
        list.add("December")


        txtMonth.setOnClickListener {
            val listPopupAdapter = ArrayAdapter(
                this@EarningFilteringActivity,
                android.R.layout.simple_spinner_dropdown_item,
                list
            )
            val popupWindow = ListPopupWindow(this@EarningFilteringActivity)
            popupWindow.setOnItemClickListener { parent, view, position, id ->
                txtMonth.text = list[position]
                month = position + 1
                popupWindow.dismiss()


            }

            popupWindow.setContentWidth(txtMonth.width)
            popupWindow.height = ListPopupWindow.WRAP_CONTENT
            popupWindow.anchorView = txtMonth
            popupWindow.setAdapter(listPopupAdapter)
            popupWindow.show()
        }


    }


    private fun updateList(position: Int) {
        villageList.removeAt(position)

    }
}
