package com.dhanuka.indhan.SyllabusHistory.Dialog

import android.annotation.SuppressLint
import android.app.AlertDialog
import android.content.Intent
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import android.util.DisplayMetrics
import android.view.View
import android.widget.Toast
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.UserTeamModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.BDOSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.dialog_add_bdo_layout.*
import java.util.*


@SuppressLint("ValidFragment")
/**
 * Created by John on 5/16/2018.
 */
class AddBDODialogActivity : AppCompatActivity(), View.OnClickListener {

    lateinit var items: ArrayList<UserTeamModel>
    internal lateinit var onSpinerItemClick: OnSpinerItemClick
    internal lateinit var alertDialog: AlertDialog
    internal var pos: Int = 0
    internal var style: Int = 0
    lateinit var bdoSpinner: BDOSpinner
    lateinit var numberSpinner: NumberSpinner
    var bdo_id: Int = 0
    var no_OfDays: Int = 0
    var bdo_name: String = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.dialog_add_bdo_layout)
        spinnerBDOName.setOnClickListener(this)
        spinnerDays.setOnClickListener(this)
        submit.setOnClickListener(this)
        val dm = DisplayMetrics()
        windowManager.defaultDisplay.getMetrics(dm)
        val width = dm.widthPixels
        val height = dm.heightPixels
        items = DatabaseManager.getAllUserTeamDataList(this)
        window.setLayout((width * .9).toInt(), (height * .5).toInt())
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.spinnerBDOName -> {
                bdoSpinner = BDOSpinner(this, items, "Select BDO")
                bdoSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        //  bdo_id=Integer.parseInt(item)
                        spinnerBDOName.text = item
                        bdo_name = item
                        bdo_id = items.get(position).id
                    }
                })
                bdoSpinner.showSpinerDialog()
            }
            R.id.spinnerDays -> {
                numberSpinner = NumberSpinner(this, Utility.getList(100), "Select no. of Days")

                numberSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        spinnerDays.text = item
                        no_OfDays = Integer.parseInt(item)
                    }
                })
                numberSpinner.showSpinerDialog()
            }
            R.id.submit -> {
                if (bdo_id == 0) {
                    Toast.makeText(this, "Please Select BDO", Toast.LENGTH_SHORT).show()
                } else if (no_OfDays == 0) {
                    Toast.makeText(this, "Please Select no. of Days", Toast.LENGTH_SHORT).show()
                } else {
                    val intent = Intent()
                    intent.putExtra("bdo_id", bdo_id)
                    intent.putExtra("nod", no_OfDays)
                    intent.putExtra("bdo_name", bdo_name)
                    setResult(1001, intent)
                    finish()
                }
            }
        }
    }
}
