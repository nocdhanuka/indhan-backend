package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDetail
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.layout_sp_model.view.*

//

class BDOPlanDetailAdapter(homeCategoryRecordResponseModelArrayList: ArrayList<PlanAtpDetail>) :
    RecyclerView.Adapter<BDOPlanDetailAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<PlanAtpDetail>

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {

        }
    }


    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_sp_model, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        // if(plan.)
        holder.itemView.product_name.text = plan.product

        if (plan.product1.isNullOrEmpty() && plan.product2.isNullOrEmpty()) {
            holder.itemView.product_name.text = plan.product + " |"

        } else {
            holder.itemView.product_name.text =
                plan.product + ", " + plan.product1 + ", " + plan.product2 + " |"

        }
        holder.itemView.crop_name.text = plan.crop
        if (!plan.village.trim().isNullOrEmpty())
            holder.itemView.address_tv.text =
                plan.village + ", " + plan.tehsil + ", " + plan.district + ", " + plan.state
        else
            holder.itemView.address_tv.text = plan.tehsil + ", " + plan.district + ", " + plan.state
        holder.itemView.details.text =
            "Farmers-" + plan.expectedFarmer + " | Acreage-" + plan.expectedPda + " | Meeting-" + plan.dataSource
        showDate(holder, position)
    }

    private fun showDate(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        var dates = ""
        if (!plan.planDate.isNullOrEmpty())
            dates = Utility.getDateForView(plan.planDate)

        if (position == 0) {
            holder.itemView.date.text = dates
            holder.itemView.date.visibility = View.VISIBLE
        } else {
            if (plan.planDate.equals(filteredData[position - 1].planDate)) {
                holder.itemView.date.visibility = View.GONE
            } else {
                holder.itemView.date.visibility = View.VISIBLE
                holder.itemView.date.text = dates
            }
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }


}
