import {DateRangePicker} from 'react-date-range';
import React from 'react';
import ReactDOM from 'react-dom';
import 'react-date-range/dist/styles.css'; // main style file
import 'react-date-range/dist/theme/default.css'; // theme css file
import locale from './DateRangeLocale';

class DateRangeSelect extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            startDate: props.start ? new Date(Number(props.start)) : new Date(),
            endDate: props.end ? new Date(Number(props.end)) : new Date(),
        };
    }

    handleSelect = (ranges) => {
        let endDate = new Date(ranges.selection.endDate.getTime());
        let startDate = ranges.selection.startDate;

        if (startDate.getTime() === endDate.getTime()) {
            endDate.setUTCDate(startDate.getUTCDate() + 1);
            endDate.setSeconds(startDate.getUTCSeconds() - 1);
        }

        this.setState({
            startDate: startDate,
            endDate: endDate,
            }
        );

        //TODO: don't use this hacky method
        window.location.href = window.location.href.split('?')[0] + '?start='  + (startDate.getTime() / 1000) + '&end='  + (endDate.getTime() / 1000);
    };

    render() {
        const selectionRange = {
            startDate: this.state.startDate,
            endDate: this.state.endDate,
            key: 'selection',
        };

        return (
            <DateRangePicker
                ranges={[selectionRange]}
                onChange={this.handleSelect}
            />
        )
    }
}

if (document.getElementById('date-range-selector')) {
    const el = document.getElementById('date-range-selector');
    const start = el.getAttribute('start');
    const end = el.getAttribute('end');
    ReactDOM.render(<DateRangeSelect locale={locale} start={start} end={end}/>, document.getElementById('date-range-selector'));
}
