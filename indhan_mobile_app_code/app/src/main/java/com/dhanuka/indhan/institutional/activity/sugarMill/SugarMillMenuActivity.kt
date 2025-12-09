package com.dhanuka.indhan.institutional.activity.sugarMill

import android.os.Bundle
import android.view.View
import androidx.core.content.res.ResourcesCompat
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.adapter.InstitutionalSubMenuAdapter
import com.dhanuka.indhan.institutional.model.InstitutionalSubMenuModel
import kotlinx.android.synthetic.main.activity_institutional_menu.*

class SugarMillMenuActivity : BaseActivity(), InstitutionalSubMenuAdapter.SubMenuClickListener {

    private var listOfSubMenu: ArrayList<InstitutionalSubMenuModel> = ArrayList()

    override fun onClick(v: View?) {

    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_institutional_menu)
        tvHeaderName.text = "Sugar Mill"
        insertItemsToList()
        ivBack.setOnClickListener { finish() }
    }

    private fun insertItemsToList() {
        listOfSubMenu.clear()

        val item1 = InstitutionalSubMenuModel()
        item1.appMenuName = "Sugar Mill"
        item1.appMenuId = 1
        item1.iconImage = ResourcesCompat.getDrawable(resources, R.drawable.sugar_estate, null)
        listOfSubMenu.add(item1)

//        val item2 = InstitutionalSubMenuModel()
//        item2.appMenuName = "ATP"
//        item2.appMenuId = 2
//        item2.iconImage =
//            ResourcesCompat.getDrawable(resources, R.drawable.plan_atp_institute, null)
//        listOfSubMenu.add(item2)

        val item3 = InstitutionalSubMenuModel()
        item3.appMenuName = "Visits"
        item3.appMenuId = 3
        item3.iconImage =
            ResourcesCompat.getDrawable(resources, R.drawable.visit_summary_institute, null)
        listOfSubMenu.add(item3)

        val item4 = InstitutionalSubMenuModel()
        item4.appMenuName = "Sales Report"
        item4.appMenuId = 4
        item4.iconImage = ResourcesCompat.getDrawable(resources, R.drawable.ic_distributor, null)
        listOfSubMenu.add(item4)

        setAdapter()
    }

    private fun setAdapter() {
        val adapter = InstitutionalSubMenuAdapter(this, listOfSubMenu, this@SugarMillMenuActivity)
        adapter.setClickListener(this)
        rvInstitution.adapter = adapter
    }

    override fun onClick(view: View, subMenu: InstitutionalSubMenuModel) {
        when (subMenu.appMenuName.toString().trim().lowercase()) {
            "sugar mill" -> {
                launchIntent(SugarMillListActivity::class.java, false)
            }
            "visits" -> {
                launchIntent(SugarMillVisitSummaryActivity::class.java, false)
            }
            "sales report" -> {
                launchIntent(SugarMillSalesReportActivity::class.java, false)
            }
        }
    }
}