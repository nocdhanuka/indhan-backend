package com.dhanuka.indhan.Reports.supervisor_report.fragments


import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup

import com.dhanuka.indhan.R
import com.dhanuka.indhan.Reports.Model.EfficiencyResponse
import com.dhanuka.indhan.Reports.supervisor_report.SupervisorReportsBean
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.fragment_efficiency.*
import java.util.*

/**
 * A simple [Fragment] subclass.
 */
class EfficiencyFragment : Fragment() {

    var list_efficieny: ArrayList<EfficiencyResponse.Efficiency> = ArrayList()
    var curr = 0
    var prev = 0
    private lateinit var data: SupervisorReportsBean.Data

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        // Inflate the layout for this fragment

        data = arguments?.getSerializable("data") as SupervisorReportsBean.Data
        return inflater.inflate(R.layout.fragment_efficiency, container, false)
    }

    override fun onActivityCreated(savedInstanceState: Bundle?) {
        super.onActivityCreated(savedInstanceState)
        setEffeciency(data)

        enable_left.setOnClickListener {
            if (prev >= 0) {
                prev--
                curr--
            }
            showEfficiency()
        }

        enable_right.setOnClickListener {
            if (curr < list_efficieny.size - 1) {
                prev++
                curr++
            }
            showEfficiency()
        }
    }


    private fun setEffeciency(model: SupervisorReportsBean.Data) {
        if (model != null) {
            var values = model.efficiencyData
            values.forEach {
                it.dateObj = Utility.getDateFromMonth(it.year + "-" + it.month)
            }
            values.sorted()
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


}
