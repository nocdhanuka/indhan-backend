package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.app.Activity
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusProduct
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_product_display.view.*

/**
 * Created by Shivam on 2/27/2018.
 */

class PlanSyllabusRCAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<SyllabusProduct>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<PlanSyllabusRCAdapter.MyViewHolder>() {


    private val filteredData: ArrayList<SyllabusProduct>

    // private lateinit var clickListener: HomeCategoryClickListener
    private lateinit var onLongclickListener: PlanSyllabusClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnLongClickListener(object : View.OnLongClickListener {
                override fun onLongClick(v: View?): Boolean {
                    onLongclickListener.onLongClick(
                        view,
                        filteredData?.get(adapterPosition)!!,
                        adapterPosition
                    )
                    return true
                }
            })
        }
    }

    fun setLongClickListener(onLongclickListener: PlanSyllabusClickListener) {
        this.onLongclickListener = onLongclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_product_display, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.product_name.text = plan.product_name
        holder.itemView.crop_name.text = plan.crops_name
        holder.itemView.days.text = plan.no_of_days.toString()
        holder.itemView.bdo.text = plan.bdo_id.toString()
        showName(holder, position)
        // holder.itemView.bdo_name.text = plan.bdo_name.toString()
    }

    private fun showName(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        if (position == 0) {
            holder.itemView.bdo_name.text = plan.bdo_name.toString()
            holder.itemView.layoutname.visibility = View.VISIBLE
        } else {
            if (plan.bdo_name.equals(filteredData[position - 1].bdo_name)) {
                holder.itemView.layoutname.visibility = View.GONE
            } else {
                holder.itemView.layoutname.visibility = View.VISIBLE
                holder.itemView.bdo_name.text = plan.bdo_name.toString()
            }
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface PlanSyllabusClickListener {
        fun onLongClick(view: View, atpDbModel: SyllabusProduct, position: Int)
    }
}
