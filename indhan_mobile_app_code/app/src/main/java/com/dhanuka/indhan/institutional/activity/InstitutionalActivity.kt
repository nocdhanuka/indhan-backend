package com.dhanuka.indhan.institutional.activity

import android.os.Bundle
import android.view.View
import androidx.core.content.res.ResourcesCompat
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.activity.influencers.InfluencersMenuActivity
import com.dhanuka.indhan.institutional.activity.sugarMill.SugarMillMenuActivity
import com.dhanuka.indhan.institutional.activity.teaEstate.TeaEstateMenuActivity
import com.dhanuka.indhan.institutional.adapter.InstitutionalSubMenuAdapter
import com.dhanuka.indhan.institutional.model.InstitutionalSubMenuModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_institutional_menu.*

class InstitutionalActivity : BaseActivity(), InstitutionalSubMenuAdapter.SubMenuClickListener {
    private var preference: Preference? = null
    private var listOfSubMenu: ArrayList<InstitutionalSubMenuModel> = ArrayList()
    private var userType: String = ""
    override fun onClick(v: View?) {

    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_institutional_menu)
        preference = Preference(this)
        userType = preference?.getString("user_type") ?: ""
        insertItemsToList()
        getCompanyDesignationFlData()
        ivBack.setOnClickListener {
            finish()
        }
    }

    private fun getCompanyDesignationFlData() {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            val getData = OfflineDatabase(this)
            getData.companyFlDesignationData()
            getData.companyFlDesignationDataSugarMill()
            getData.companyFlDesignationDataInfluencers()
        }
    }

    private fun insertItemsToList() {
        val item1 = InstitutionalSubMenuModel()
        item1.appMenuName = "Tea Estates"
        item1.appMenuId = 1
        item1.iconImage = ResourcesCompat.getDrawable(resources, R.drawable.tea_estate, null)
        listOfSubMenu.add(item1)

        val item2 = InstitutionalSubMenuModel()
        item2.appMenuName = "Sugar Mills"
        item2.appMenuId = 2
        item2.iconImage = ResourcesCompat.getDrawable(resources, R.drawable.sugar_estate, null)
        listOfSubMenu.add(item2)

        val item3 = InstitutionalSubMenuModel()
        item3.appMenuName = "Influencers"
        item3.appMenuId = 3
        item3.iconImage = ResourcesCompat.getDrawable(resources, R.drawable.influencers, null)
        listOfSubMenu.add(item3)

        val item4 = InstitutionalSubMenuModel()
        item4.appMenuName = "Testimonials"
        item4.appMenuId = 4
        item4.iconImage = ResourcesCompat.getDrawable(resources, R.drawable.ic_distributor, null)
        listOfSubMenu.add(item4)

        val item5 = InstitutionalSubMenuModel()
        item5.appMenuName = "Circulars"
        item5.appMenuId = 4
        item5.iconImage = ResourcesCompat.getDrawable(resources, R.drawable.circulars, null)
        listOfSubMenu.add(item5)

        val item6 = InstitutionalSubMenuModel()
        item6.appMenuName = "Distributor Connect"
        item6.appMenuId = 6
        item6.iconImage =
            ResourcesCompat.getDrawable(resources, R.drawable.ic_distributor, null)
        listOfSubMenu.add(item6)

        setAdapter()
    }

    override fun onClick(view: View, subMenu: InstitutionalSubMenuModel) {
        when (subMenu.appMenuName.toString().trim().lowercase()) {

            "tea estates" -> {
                launchIntent(TeaEstateMenuActivity::class.java, false)
            }
            "sugar mills" -> {
                launchIntent(SugarMillMenuActivity::class.java, false)
            }
            "influencers" -> {
                launchIntent(InfluencersMenuActivity::class.java, false)
            }
            "testimonials" -> {
                launchIntent(TestimonialsListActivity::class.java, false)
            }
            "circulars" -> {
                launchIntent(CircularsListActivity::class.java, false)
            }
            "distributor connect" -> {
                launchIntent(DistributorsConnectActivity::class.java, false)
            }
        }
    }

    fun setAdapter() {
        val adapter = InstitutionalSubMenuAdapter(this, listOfSubMenu, this@InstitutionalActivity)
        adapter.setClickListener(this)
        rvInstitution.adapter = adapter
    }
}