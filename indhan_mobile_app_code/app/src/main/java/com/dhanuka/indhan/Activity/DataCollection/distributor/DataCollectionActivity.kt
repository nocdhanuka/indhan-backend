package com.dhanuka.indhan.Activity.DataCollection.distributor

import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.DataCollection.chemical.ChemicalDataCollection
import com.dhanuka.indhan.R
import com.dhanuka.indhan.support.adapter.SubMenuAdapter
import com.dhanuka.indhan.support.model.SupportSubMenuBean
import kotlinx.android.synthetic.main.activity_data_collection.*
import java.util.*
import kotlin.collections.ArrayList

class DataCollectionActivity : BaseActivity(), SubMenuAdapter.SubMenuClickListener {
    var listOfSubMenu = ArrayList<SupportSubMenuBean>()

    override fun onClick(v: View?) {

    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_data_collection)
        val item1 = SupportSubMenuBean()
        item1.campaignTypeName = "Channels"
        listOfSubMenu.add(item1)
        val item2 = SupportSubMenuBean()
        item2.campaignTypeName = "Molecules"
        listOfSubMenu.add(item2)
        val adapter = SubMenuAdapter(this, listOfSubMenu, this@DataCollectionActivity)
        adapter.setClickListener(this)
        rvDataCollection.adapter = adapter
    }

    override fun onClick(view: View, subMenu: SupportSubMenuBean) {
        when (subMenu.campaignTypeName.toString().trim().toLowerCase(Locale.getDefault())) {
            "channels" -> {
                launchIntent(DistributorDashBoard::class.java, false)
            }
            "molecules" -> {
                launchIntent(ChemicalDataCollection::class.java, false)
            }
        }
    }
}