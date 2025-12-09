package com.dhanuka.indhan.RegisterFarmerScreen

import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.R
import com.dhanuka.indhan.RegisterFarmerScreen.farmerRegistrationMenu.FarmerRegistrationSubMenuModel
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.IfcFarmerRegistrationActivity
import com.dhanuka.indhan.RegisterFarmerScreen.normalFarmerRegistration.FarmerRegisterActivity
import kotlinx.android.synthetic.main.farmer_registration_sub_menu.*
import java.util.*
import kotlin.collections.ArrayList

class FarmerRegistrationMenuActivity : BaseActivity(), View.OnClickListener,
    FarmerRegistrationSubMenuAdapter.SubMenuClickListener {
    var listOfSubMenu = ArrayList<FarmerRegistrationSubMenuModel>()

    override fun onClick(v: View?) {

    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.farmer_registration_sub_menu)
        val farmerMenu1 =
            FarmerRegistrationSubMenuModel()
        farmerMenu1.name = "IFC Farmer Registration"
        farmerMenu1.image = intent?.getStringExtra("image") ?: ""
        listOfSubMenu.add(farmerMenu1)
        val farmerMenu2 =
            FarmerRegistrationSubMenuModel()
        farmerMenu2.name = "Farmer Registration"
        farmerMenu2.image = intent?.getStringExtra("image") ?: ""
        listOfSubMenu.add(farmerMenu2)

        rvFarmerSubMeu.adapter = FarmerRegistrationSubMenuAdapter(
            this,
            listOfSubMenu,
            this@FarmerRegistrationMenuActivity
        )
    }

    override fun onClickMenu(view: View, subMenu: FarmerRegistrationSubMenuModel) {
        when (subMenu.name.toString().trim().toLowerCase(Locale.getDefault())) {
            "ifc farmer registration" -> {
                launchIntent(IfcFarmerRegistrationActivity::class.java, false)
            }
            "farmer registration" -> {
                launchIntent(FarmerRegisterActivity::class.java, false)
            }
        }
    }
}