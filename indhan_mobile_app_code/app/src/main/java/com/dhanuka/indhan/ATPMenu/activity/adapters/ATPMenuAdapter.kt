package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.app.Activity
import android.content.Intent
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.ATPMenu.activity.ATPBean
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.ATPMenu.activity.activities.ATPDetailsActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.layout_atp_model.view.*
import java.io.Serializable

//

class ATPMenuAdapter(
    list: ArrayList<ATPBean.Data>,
    private val mActivityContext: Activity,
    val clickListener: OnClickListener
) : RecyclerView.Adapter<ATPMenuAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<ATPBean.Data>
    private var isPending: Boolean = false
    var preference: Preference

    init {
        this.filteredData = list
        preference = Preference(mActivityContext)

    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                //onclickListener.onSelectedActivate(filteredData[adapterPosition])
            }
        }
    }

    fun setClickListener(onclickListener: ActivateTicketClickListener) {

        //this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_atp_model, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        val designation: String = preference.getString("designation")

        if (designation.equals("COUNTERPART-FL", true)
            || designation.equals("CS- FL", true)
            || designation.equals("C-SFL", true)
        ) {
            if (plan.forwhom == "2") {
                holder.itemView.product_name.text = plan.plan_atp_id + "(Counterpart -FL)"
            } else {
                holder.itemView.product_name.text = plan.plan_atp_id + "(BDO-FL)"
            }
        } else if (designation.equals("BDO-FL", true)
            || designation.equals("BD officer", true)
            || designation.equals("BDO", true)
            || designation.equals("BDOS", true)
        ) {
            if (plan.forwhom == "1") {
                holder.itemView.product_name.text = plan.plan_atp_id + "(BDO-FL)"
            } else {
                holder.itemView.product_name.text = plan.plan_atp_id + "(Counterpart -FL)"
            }
        } else {
            if (plan.forwhom == "1") {
                holder.itemView.product_name.text = plan.plan_atp_id + "(BDO-FL)"
            } else {
                holder.itemView.product_name.text = plan.plan_atp_id + "(Counterpart -FL)"
            }
        }

        holder.itemView.crop_name.visibility = View.GONE
        holder.itemView.view.visibility = View.GONE
        holder.itemView.address_tv.text = plan.tehsil + ", " + plan.district + ", " + plan.state
        holder.itemView.details.text =
            "Farmers - " + plan.expected_farmer.toString() + " PDA - " + plan.expected_pda + " Meeting - " + plan.data_source_id
        val dates = Utility.getDateForView(plan.plan_date)
        if (position == 0) {
            holder.itemView.date.text = dates
            holder.itemView.date.visibility = View.VISIBLE
        } else {

            if (Utility.getDateForView(plan.plan_date)
                    .equals(Utility.getDateForView(filteredData[position - 1].plan_date))
            ) {
                holder.itemView.date.visibility = View.GONE
            } else {
                holder.itemView.date.visibility = View.VISIBLE
                holder.itemView.date.text = dates
            }
        }
        if (plan.plan_date.contains("29")) {
            var i = 0
        }
        if (plan.status == "3") {
            isPending = true
            holder.itemView.ivActive.visibility = View.VISIBLE
            holder.itemView.ivActive.text = "No Reason Provided"
            holder.itemView.ivActive.setBackgroundColor(
                ContextCompat.getColor(
                    mActivityContext,
                    R.color.orange
                )
            )
            holder.itemView.ivActive.setOnClickListener {
                clickListener.onClick(position)
            }
        } else if (plan.status == "0") {
            holder.itemView.ivActive.visibility = View.GONE
        } else if (plan.status == "2") {
            if (plan.reason != "") {
                holder.itemView.ivActive.visibility = View.VISIBLE
                holder.itemView.ivActive.text = "Reason Provided"
                holder.itemView.ivActive.setBackgroundColor(
                    ContextCompat.getColor(
                        mActivityContext,
                        R.color.yello
                    )
                )
            } else {
                holder.itemView.ivActive.visibility = View.VISIBLE
                holder.itemView.ivActive.setBackgroundColor(
                    ContextCompat.getColor(
                        mActivityContext,
                        R.color.yello
                    )
                )
                holder.itemView.ivActive.text = "Activated"
            }
        } else if (plan.status == "1") {
            holder.itemView.ivActive.visibility = View.VISIBLE
            holder.itemView.ivActive.setBackgroundColor(
                ContextCompat.getColor(
                    mActivityContext,
                    R.color.colorPrimary
                )
            )
            holder.itemView.ivActive.text = "Activate"
        }
        holder.itemView.lldata.setOnClickListener {
            var intent = Intent(mActivityContext, ATPDetailsActivity::class.java)
            intent.putExtra(Constants.ATP_DETAIL, filteredData[position] as Serializable)
            mActivityContext.startActivity(intent)
        }
    }


    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface ActivateTicketClickListener {
        fun onSelectedActivate(model: ATPBean.Data)
    }

}
