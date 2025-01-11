(function (wp) {
    const {registerPlugin} = wp.plugins;
    const {PluginSidebar} = wp.editor;
    const {PanelBody, TextControl, Button, Spinner} = wp.components;
    const {withSelect, withDispatch} = wp.data;
    const {createElement, useState} = wp.element;

    const GeocodingSidebar = ({postTitle, setPostTitle}) => {
        const [searchQuery, setSearchQuery] = useState('');
        const [loading, setLoading] = useState(false);
        const [error, setError] = useState('');
        const [cities, setCities] = useState([]);
        const [selectedCity, setSelectedCity] = useState(null);

        const handleSearch = async () => {
            if (!searchQuery) {
                setError('City name');
                return;
            }
            setLoading(true);
            setError('');
            setCities([]);

            const apiKey = '5796abbde9106b7da4febfae8c44c232';
            const apiUrl = `https://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(
                searchQuery
            )}&limit=5&appid=${apiKey}`;

            try {
                const response = await fetch(apiUrl);
                const data = await response.json();

                if (data && data.length > 0) {
                    setCities(data);
                } else {
                    setError('City not found');
                }
            } catch (err) {
                setError('Error fetching city data');
            } finally {
                setLoading(false);
            }
        };

        const handleCitySelect = (city) => {
            setSelectedCity(city);
            setPostTitle(city.name);

            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');

            if (latitudeInput) {
                latitudeInput.value = city.lat;
            }

            if (longitudeInput) {
                longitudeInput.value = city.lon;
            }
            //setCities([]);
        };

        return createElement(
            PluginSidebar,
            {name: 'geocoding-sidebar', title: 'City Search', icon: 'location-alt'},
            createElement(
                PanelBody,
                {title: 'Search', initialOpen: true},
                createElement(TextControl, {
                    label: 'City name',
                    value: searchQuery,
                    onChange: (value) => setSearchQuery(value),
                    onBlur: handleSearch,
                }),
                createElement(
                    Button,
                    {
                        isPrimary: true,
                        onClick: handleSearch,
                        disabled: loading,
                        style: {marginTop: '10px'},
                    },
                    loading ? createElement(Spinner) : 'Search'
                ),
                error && createElement('p', {style: {color: 'red', marginTop: '10px'}}, error)
            ),
            createElement(
                PanelBody,
                {title: 'Search results', initialOpen: true},
                cities.length > 0
                    ? createElement(
                        'ul',
                        {style: {listStyleType: 'none', padding: 0}},
                        cities.map((city) =>
                            createElement(
                                'li',
                                {
                                    key: city.id,
                                    onClick: () => handleCitySelect(city),
                                    style: {
                                        cursor: 'pointer',
                                        marginBottom: '8px',
                                        padding: '5px',
                                        border: '1px solid #ddd',
                                        borderRadius: '4px',
                                    },
                                },
                                `${city.country ? city.country + ', ' : ''}${city.state ? city.state + ', ' : ''}${city.name}`
                            )
                        )
                    )
                    : createElement('p', null, 'City not found')
            )
        );
    };

    const EnhancedGeocodingSidebar = withSelect((select) => ({
        postTitle: select('core/editor').getEditedPostAttribute('title'),
    }))(
        withDispatch((dispatch) => ({
            setPostTitle: (title) =>
                dispatch('core/editor').editPost({title}),
        }))(GeocodingSidebar)
    );

    registerPlugin('geocoding-sidebar', {
        render: EnhancedGeocodingSidebar,
    });
})(window.wp);
