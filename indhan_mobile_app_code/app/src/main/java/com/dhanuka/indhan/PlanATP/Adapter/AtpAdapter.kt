package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.app.Activity
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDBModel
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.layout_atp_model.view.*

//

class AtpAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<PlanAtpDBModel>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<AtpAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<PlanAtpDBModel>
    lateinit var onclickListener: BDODescriptorClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {

        }
    }

    fun setClickListener(onclickListener: BDODescriptorClickListener) {

        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_atp_model, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.product_name.text = plan.product
        showDate(holder, position)
        holder.itemView.crop_name.text = plan.crop
        holder.itemView.address_tv.text =
            plan.village + ", " + plan.tehsil + ", " + plan.district + ", \n" + plan.state
        holder.itemView.details.text =
            "Farmers - " + plan.expected_farmer.toString() + " PDA - " + plan.expected_pda + " Meeting - " + plan.data_source
        if (plan.isUploaded != 0) {
            holder.itemView.status.visibility = View.VISIBLE
            holder.itemView.status.text = "Shared with your S1"
        } else {
            holder.itemView.status.visibility = View.GONE
        }

    }

    private fun showDate(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        val dates = Utility.getDateForView(plan.date)

        if (position == 0)
            holder.itemView.date.text = dates
        else if (position < filteredData.size) {
            if (plan.date.equals(filteredData[position - 1].date)) {
                holder.itemView.date.visibility = View.GONE
            } else {
                holder.itemView.date.text = dates
            }

        }


    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface BDODescriptorClickListener {
    }

}
